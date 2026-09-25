@extends('layouts.admin')

@section('title', 'AI Assistant - Admin - Savora Cafeteria')

@section('admin_content')
    <section class="admin-page-heading"><div><span class="admin-eyebrow">Authorized assistant</span><h1>AI Assistant</h1><p>Ask questions about cafeteria data available to your authenticated admin account.</p></div></section>
    <section class="admin-management-section admin-ai-section"><div class="admin-ai-form"><textarea id="adminAiInput" rows="6" placeholder="How many orders did we receive today?"></textarea><button type="button" class="admin-primary-button" id="adminAiAsk"><i class="bi bi-stars"></i> Ask Savora AI</button><div id="adminAiResult" class="admin-ai-result"></div></div></section>
@endsection
