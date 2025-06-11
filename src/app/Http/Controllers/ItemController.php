<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Address;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ExhibitionRequest;
use App\Http\Requests\PurchaseRequest;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::query();

        // キーワード検索
        if ($request->has('keyword')) {
            $keyword = $request->input('keyword');
            $query->where('name', 'like', "%{$keyword}%");
        }

        // マイリスト表示の場合
        if ($request->has('page') && $request->input('page') === 'mylist') {
            if (auth()->check()) {
            $query->whereHas('favorites', function($q) {
                $q->where('user_id', auth()->id());
            });
            } else {
                // 未認証ユーザーの場合は空のクエリを返す
                $query->whereRaw('1 = 0');
            }
        }

        // 自分の商品は表示しない
        if (auth()->check()) {
            $query->where('user_id', '!=', auth()->id());
        }

        $items = $query->latest()->get();

        return view('items.index', compact('items'));
    }

    public function show(Item $item)
    {
        $item->load('categories', 'condition', 'comments.user', 'favorites');
        
        // いいね済みかどうかをチェック
        $isLiked = false;
        if (auth()->check()) {
            $isLiked = $item->favorites()->where('user_id', auth()->id())->exists();
        }
        
        return view('items.show', compact('item', 'isLiked'));
    }

    public function favorite(Request $request, Item $item)
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        $existing = $item->favorites()->where('user_id', $user->id)->first();
        
        if ($existing) {
            // いいね解除
            $existing->delete();
        } else {
            // いいね追加
            $item->favorites()->create([
                'user_id' => $user->id
            ]);
        }
        
        return redirect()->route('items.show', $item->id);
    }

    public function comment(CommentRequest $request, Item $item)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $item->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content
        ]);
        return redirect()->route('items.show', $item->id);
    }

    public function purchase(PurchaseRequest $request, Item $item)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // ユーザーの住所（1件目）を取得
        $sessionAddress = session('purchase_address');
        if ($sessionAddress) {
            $addressData = $sessionAddress;
        } else {
            $addressModel = Address::where('user_id', auth()->id())->first();
            $addressData = [
                'postal_code' => $addressModel->postal_code ?? '',
                'address'     => $addressModel->address ?? '',
                'building'    => $addressModel->building ?? '',
            ];
        }

        $payment_method = $request->payment_method ?? 'コンビニ払い';
        return view('items.purchase', compact('item', 'addressData', 'payment_method'));
    }

    public function purchaseStore(Request $request, Item $item)
    {
        // 未ログインならログイン画面へ
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // ユーザーの住所（1件目）を取得
        $sessionAddress = session('purchase_address');
        if ($sessionAddress) {
            $addressData = $sessionAddress;
        } else {
            $addressModel = Address::where('user_id', auth()->id())->first();
            $addressData = [
                'postal_code' => $addressModel->postal_code ?? '',
                'address'     => $addressModel->address ?? '',
                'building'    => $addressModel->building ?? '',
            ];
        }

        // すでに購入済みなら処理しない
        if ($item->purchases()->exists()) {
            return redirect()->route('items.show', $item->id)->with('error', 'この商品はすでに購入されています。');
        }

        // 支払い方法のバリデーション
        $request->validate([
            'payment_method' => ['required', 'string', 'in:クレジットカード,コンビニ払い'],
        ]);

        try {
            if ($request->payment_method === 'クレジットカード') {
                // Stripeの決済処理
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

                // Checkoutセッションを作成
                $session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => 'jpy',
                            'product_data' => [
                                'name' => $item->name,
                            ],
                            'unit_amount' => $item->price,
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => route('items.purchase.success', ['item' => $item->id, 'session_id' => '{CHECKOUT_SESSION_ID}']),
                    'cancel_url' => route('items.purchase', $item->id),
                    'metadata' => [
                        'item_id' => $item->id,
                        'user_id' => auth()->id(),
                        'address' => json_encode($addressData),
                    ],
                ]);

                // セッションIDを保存
                session(['stripe_session_id' => $session->id]);
                // 住所情報は決済後に保存するのでここでは削除しない

                // Checkoutページにリダイレクト
                return redirect($session->url);
            } else {
                // コンビニ払いの場合
                // 住所を検索または作成
                $address = Address::firstOrCreate(
                    [
                        'user_id' => auth()->id(),
                        'postal_code' => $addressData['postal_code'],
                        'address' => $addressData['address'],
                        'building' => $addressData['building'],
                    ]
                );

                // 購入情報を保存
                $purchase = $item->purchases()->create([
                    'user_id' => auth()->id(),
                    'address_id' => $address->id,
                    'payment_method' => $request->payment_method,
                    'postal_code' => $addressData['postal_code'],
                    'address' => $addressData['address'],
                    'building' => $addressData['building'],
                ]);

                // 商品を売却済みに更新
                $item->update(['is_sold' => true]);
                // セッションの住所情報を削除
                session()->forget('purchase_address');

                return redirect()->route('items.show', $item->id)->with('success', '購入が完了しました！コンビニでのお支払いをお願いします。');
            }
        } catch (\Exception $e) {
            return redirect()->route('items.purchase', $item->id)
                ->with('error', '決済処理中にエラーが発生しました: ' . $e->getMessage());
        }
    }

    public function purchaseSuccess(Request $request, Item $item)
    {
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $session = \Stripe\Checkout\Session::retrieve($request->session_id);

            if ($session->payment_status === 'paid') {
                // 購入情報を保存
                $addressData = json_decode($session->metadata->address, true);
                $item->purchases()->create([
                    'user_id' => auth()->id(),
                    'address_id' => Address::where('user_id', auth()->id())->first()->id ?? null,
                    'payment_method' => 'クレジットカード',
                    'stripe_payment_id' => $session->payment_intent,
                    'postal_code' => $addressData['postal_code'] ?? '',
                    'address' => $addressData['address'] ?? '',
                    'building' => $addressData['building'] ?? '',
                ]);
                // 商品のis_soldフラグを更新
                $item->update(['is_sold' => true]);
                // セッションの住所情報を削除
                session()->forget('purchase_address');

                return redirect()->route('items.show', $item->id)->with('success', '購入が完了しました！');
            }
        } catch (\Exception $e) {
            return redirect()->route('items.show', $item->id)
                ->with('error', '決済の確認中にエラーが発生しました: ' . $e->getMessage());
        }
    }

    public function create()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', '商品を出品するにはログインが必要です。');
        }
        
        $categories = Category::all();
        $conditions = Condition::all();
        return view('items.create', compact('categories', 'conditions'));
    }

    public function store(ExhibitionRequest $request)
    {
        try {
            if (!auth()->check()) {
                return redirect()->route('login')->with('error', '商品を出品するにはログインが必要です。');
            }

            $validated = $request->validated();

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('items', 'public');
            }

            $item = Item::create([
                'user_id' => auth()->id(),
                'name' => $validated['name'],
                'brand_name' => $request->input('brand_name'),
                'description' => $validated['description'],
                'price' => $validated['price'],
                'condition_id' => $validated['condition_id'],
                'image_url' => $imagePath ? $imagePath : null,
            ]);

            // カテゴリーの関連付け
            $item->categories()->attach($validated['categories']);

            return redirect()->route('items.show', $item->id)->with('success', '商品を出品しました');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function optimizeImage($image)
    {
        $path = storage_path('app/public/' . $image);
        $info = getimagesize($path);
        
        // 画像の最適化
        if ($info['mime'] === 'image/jpeg') {
            $image = imagecreatefromjpeg($path);
        } elseif ($info['mime'] === 'image/png') {
            $image = imagecreatefrompng($path);
        }

        // リサイズ
        $maxWidth = 800;
        $maxHeight = 800;
        $width = imagesx($image);
        $height = imagesy($image);

        if ($width > $maxWidth || $height > $maxHeight) {
            $ratio = min($maxWidth / $width, $maxHeight / $height);
            $newWidth = $width * $ratio;
            $newHeight = $height * $ratio;

            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            
            // WebP形式で保存
            $webpPath = str_replace(['.jpg', '.jpeg', '.png'], '.webp', $path);
            imagewebp($newImage, $webpPath, 80);
            
            // 2xサイズの画像も生成
            $newImage2x = imagecreatetruecolor($newWidth * 2, $newHeight * 2);
            imagecopyresampled($newImage2x, $image, 0, 0, 0, 0, $newWidth * 2, $newHeight * 2, $width, $height);
            
            $webpPath2x = str_replace('.webp', '@2x.webp', $webpPath);
            imagewebp($newImage2x, $webpPath2x, 80);

            imagedestroy($newImage);
            imagedestroy($newImage2x);
        }

        imagedestroy($image);
    }
}
