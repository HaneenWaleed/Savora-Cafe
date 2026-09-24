@extends('layouts.app')

@section('title', 'Contact Us - Savora Cafeteria')

@section('content')
    <div class="contact-shell page-shell">
        <header class="contact-hero reveal">
            <div class="hero-copy">
                <span class="page-kicker">Get in touch</span>
                <h1>Contact <span>Us</span></h1>
                <p>We’d love to hear from you! Whether you have a question, feedback, or just want to say hello, we’re here for you.</p>
            </div>

            <div class="hero-photo" aria-hidden="true"></div>
        </header>

        <section class="contact-panel reveal">
            <div class="contact-form-box">
                <span class="panel-tag">Send us a message</span>
                <h2>We’re Here to Help</h2>
                <p>Fill out the form below and we’ll get back to you as soon as possible.</p>

                <form class="savora-form">
                    <div class="field-row two-col">
                        <label>
                            <span><i class="bi bi-person"></i> Your Name</span>
                            <input type="text" placeholder="Your Name">
                        </label>
                        <label>
                            <span><i class="bi bi-envelope"></i> Your Email</span>
                            <input type="email" placeholder="Your Email">
                        </label>
                    </div>

                    <label>
                        <span><i class="bi bi-telephone"></i> Your Phone (optional)</span>
                        <input type="tel" placeholder="Your Phone (optional)">
                    </label>

                    <label>
                        <span><i class="bi bi-chat-left-text"></i> Your Message</span>
                        <textarea rows="5" placeholder="Your Message"></textarea>
                    </label>

                    <button type="submit" class="btn btn-savora btn-block">Send Message <i class="bi bi-arrow-right"></i></button>
                </form>
            </div>

            <aside class="contact-info-box">
                <span class="panel-tag">Our information</span>

                <ul class="contact-list">
                    <li>
                        <span class="icon-pill"><i class="bi bi-geo-alt"></i></span>
                        <div>
                            <strong>Location</strong>
                            <small>123 Cafe Street,<br> Cairo, Egypt</small>
                        </div>
                    </li>
                    <li>
                        <span class="icon-pill"><i class="bi bi-envelope"></i></span>
                        <div>
                            <strong>Email</strong>
                            <small>info@savora.com</small>
                        </div>
                    </li>
                    <li>
                        <span class="icon-pill"><i class="bi bi-telephone"></i></span>
                        <div>
                            <strong>Phone</strong>
                            <small>+2010 1234 5678</small>
                        </div>
                    </li>
                    <li>
                        <span class="icon-pill"><i class="bi bi-clock"></i></span>
                        <div>
                            <strong>Working Hours</strong>
                            <small>Daily 8:00 AM – 10:00 PM</small>
                        </div>
                    </li>
                </ul>

                <div class="info-card-image">
                    <div class="mini-brand">Savora <small>CAFETERIA</small></div>
                </div>

                <div class="social-row">
                    <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" aria-label="X"><i class="bi bi-twitter-x"></i></a>
                </div>
            </aside>
        </section>
    </div>
@endsection
