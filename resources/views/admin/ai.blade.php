@extends('layouts.admin')

@section('title', 'AI Assistant - Admin - Savora Cafeteria')

@section('admin_content')
    <section class="admin-page-heading">
        <div>
            <span class="admin-eyebrow">Authorized assistant</span>
            <h1>AI Assistant</h1>
            <p>Ask questions about cafeteria data available to your authenticated admin account.</p>
        </div>
    </section>

    <section class="admin-management-section admin-ai-section">
        <div class="admin-chat-shell">
            <div class="admin-chat-messages" id="adminAiMessages">
                <div class="admin-chat-bubble admin-chat-bubble-ai">
                    <div class="admin-chat-avatar"><i class="bi bi-stars"></i></div>
                    <div class="admin-chat-text">Hi! Ask me anything about your cafeteria data — orders, sales, stock, customers...</div>
                </div>
            </div>

            <form class="admin-chat-input-row" id="adminAiForm">
                <textarea id="adminAiInput" rows="1" placeholder="How many orders did we receive today?"></textarea>
                <button type="submit" class="admin-chat-send" id="adminAiAsk" aria-label="Send">
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>
        </div>
    </section>
@endsection
