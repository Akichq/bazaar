@extends('layouts.app')

@section('content')
<div style="text-align:center; margin-top: 60px;">
    <img src="/logo.png" alt="COACHTECH" style="height:40px; margin-bottom:40px;">
    <p>登録していただいたメールアドレスに認証メールを送付しました。<br>メール認証を完了してください。</p>
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" style="margin: 20px 0; padding: 10px 30px; font-size: 16px;">認証はこちらから</button>
    </form>
    <a href="{{ route('verification.send') }}" style="color:#007bff; text-decoration:underline; font-size:14px;">認証メールを再送する</a>
</div>
@endsection 