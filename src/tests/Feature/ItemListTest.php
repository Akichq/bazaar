<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Condition;
use App\Models\Category;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemListTest extends TestCase
{
    use RefreshDatabase;

    public function test_全商品を取得できる()
    {
        $this->seed();
        $item = Item::first();
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee($item->name);
    }

    public function test_購入済み商品はSoldと表示される()
    {
        $this->seed();
        $item = Item::first();
        $user = User::first();
        $address = Address::first();
        if (!$address) {
            $address = Address::create([
                'user_id' => $user->id,
                'postal_code' => '123-4567',
                'address' => '東京都テスト区1-2-3',
                'building' => 'テストビル101'
            ]);
        }
        // 購入レコードを作成
        $item->purchases()->create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'payment_method' => 'テスト',
            'stripe_payment_id' => null,
        ]);
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('SOLD');
    }

    public function test_自分が出品した商品は表示されない()
    {
        $this->seed();
        $user = User::first();
        $myItem = Item::where('user_id', $user->id)->first();
        $otherItem = Item::where('user_id', '!=', $user->id)->first();
        $this->actingAs($user);
        $response = $this->get('/');
        $response->assertStatus(200);
        if ($myItem) {
            $response->assertDontSee($myItem->name);
        }
        if ($otherItem) {
            $response->assertSee($otherItem->name);
        }
    }
} 