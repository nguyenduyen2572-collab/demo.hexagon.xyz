{{-- HEXAGON Footer Premium V5: compact informational footer, no top CTA or fake newsletter. --}}
<footer class="hx-footer hx-footer-premium hx-footer-v5" id="siteFooter">
    <div class="hx-f5-container">
        <div class="hx-f5-grid">
            <section class="hx-f5-brand" aria-label="HEXAGON">
                <a class="hx-f5-logo" href="{{ url('/') }}" aria-label="HEXAGON — Trang chủ">
                    <svg viewBox="0 0 46 46" width="42" height="42" fill="none" aria-hidden="true" focusable="false">
                        <path d="M23 2.8 41 13.2v19.6L23 43.2 5 32.8V13.2L23 2.8Z" fill="#2767D5" stroke="#9BCBFF" stroke-width="1.7"/>
                        <path d="M15 15v16m16-16v16M15 23h16" stroke="#FFF" stroke-width="3.7" stroke-linecap="round"/>
                    </svg>
                    <span>HEXAGON<span class="hx-f5-dot">.</span></span>
                </a>
                <p class="hx-f5-desc" data-hx-vi="Nền tảng tên miền và giải pháp số của Công ty Cổ phần Lục Giác." data-hx-en="Domain names and digital solutions by Luc Giac Joint Stock Company.">Nền tảng tên miền và giải pháp số của Công ty Cổ phần Lục Giác.</p>
                <div class="hx-f5-chips" aria-label="HEXAGON services">
                    <span>DOMAIN</span><span>SAAS</span><span>HEXAGON</span>
                </div>
                <div class="hx-f5-benefits" aria-label="Tiện ích HEXAGON">
                    <div class="hx-f5-benefit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m13 2-9 12h7l-1 8 10-12h-7l0-8Z"/></svg>
                        <span data-hx-vi="Tra cứu nhanh" data-hx-en="Quick lookup">Tra cứu nhanh</span>
                    </div>
                    <div class="hx-f5-benefit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z"/><path d="m9 12 2 2 4-4"/></svg>
                        <span data-hx-vi="Quản lý rõ ràng" data-hx-en="Easy management">Quản lý rõ ràng</span>
                    </div>
                    <div class="hx-f5-benefit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 2"/></svg>
                        <span data-hx-vi="Dễ sử dụng" data-hx-en="Easy to use">Dễ sử dụng</span>
                    </div>
                </div>
            </section>

            <nav class="hx-f5-links" aria-label="Giới thiệu HEXAGON">
                <h3><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 3 9 5-9 5-9-5 9-5ZM3 12l9 5 9-5M3 16l9 5 9-5"/></svg><span data-hx-vi="Giới thiệu" data-hx-en="About">Giới thiệu</span></h3>
                <a href="{{ url('/') }}" data-hx-vi="Về HEXAGON" data-hx-en="About HEXAGON">Về HEXAGON</a>
                <a href="{{ url('/') }}#solutions" data-hx-vi="Giải pháp số" data-hx-en="Digital solutions">Giải pháp số</a>
                <a href="{{ url('/') }}#how-it-works" data-hx-vi="Quy trình sử dụng" data-hx-en="How it works">Quy trình sử dụng</a>
                <a href="{{ url('/') }}#support" data-hx-vi="Liên hệ & hỗ trợ" data-hx-en="Contact & support">Liên hệ & hỗ trợ</a>
            </nav>

            <nav class="hx-f5-links" aria-label="Sản phẩm HEXAGON">
                <h3><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 2 9 5-9 5-9-5 9-5ZM3 7v10l9 5 9-5V7M12 12v10"/></svg><span data-hx-vi="Sản phẩm" data-hx-en="Products">Sản phẩm</span></h3>
                <a href="{{ url('/') }}#domain-search" data-hx-vi="Tên miền" data-hx-en="Domains">Tên miền</a>
                <a href="{{ url('/') }}#pricing" data-hx-vi="Bảng giá tên miền" data-hx-en="Domain pricing">Bảng giá tên miền</a>
                @if(\Illuminate\Support\Facades\Route::has('saas.index'))
                    <a href="{{ route('saas.index') }}" data-hx-vi="Giải pháp SaaS" data-hx-en="SaaS solutions">Giải pháp SaaS</a>
                @else
                    <a href="{{ url('/') }}#solutions" data-hx-vi="Giải pháp SaaS" data-hx-en="SaaS solutions">Giải pháp SaaS</a>
                @endif
                <a href="{{ route('cart.index') }}" data-hx-vi="Giỏ hàng" data-hx-en="Shopping cart">Giỏ hàng</a>
            </nav>

            <nav class="hx-f5-links" aria-label="Tài nguyên và hướng dẫn">
                <h3><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 7c-2-2-5-3-9-3v15c4 0 7 1 9 3m0-15c2-2 5-3 9-3v15c-4 0-7 1-9 3m0-15v15"/></svg><span data-hx-vi="Tài nguyên" data-hx-en="Resources">Tài nguyên</span></h3>
                <a href="{{ url('/') }}#how-it-works" data-hx-vi="Hướng dẫn đặt hàng" data-hx-en="Ordering guide">Hướng dẫn đặt hàng</a>
                <a href="{{ url('/') }}#domain-search" data-hx-vi="Kiểm tra tên miền" data-hx-en="Domain lookup">Kiểm tra tên miền</a>
                <a href="{{ url('/') }}#pricing" data-hx-vi="Thông tin bảng giá" data-hx-en="Pricing information">Thông tin bảng giá</a>
                <a href="{{ url('/') }}#support" data-hx-vi="Câu hỏi thường gặp" data-hx-en="Frequently asked questions">Câu hỏi thường gặp</a>
            </nav>

            <nav class="hx-f5-links" aria-label="Hỗ trợ tài khoản">
                <h3><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 14v-3a8 8 0 0 1 16 0v3M4 14H2v5h5v-5H4Zm16 0h2v5h-5v-5h3ZM20 19c0 3-3 3-6 3h-2"/></svg><span data-hx-vi="Hỗ trợ" data-hx-en="Support">Hỗ trợ</span></h3>
                <a href="{{ url('/') }}#support" data-hx-vi="Trung tâm trợ giúp" data-hx-en="Help centre">Trung tâm trợ giúp</a>
                @auth
                    @if(auth()->user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}" data-hx-vi="Trang quản trị" data-hx-en="Admin dashboard">Trang quản trị</a>
                    @else
                        <a href="{{ route('customer.account.index') }}" data-hx-vi="Tài khoản của tôi" data-hx-en="My account">Tài khoản của tôi</a>
                        <a href="{{ route('customer.account.orders.index') }}" data-hx-vi="Đơn hàng của tôi" data-hx-en="My orders">Đơn hàng của tôi</a>
                    @endif
                @else
                    <a href="{{ route('customer.login') }}" data-hx-vi="Đăng nhập" data-hx-en="Sign in">Đăng nhập</a>
                    <a href="{{ route('customer.register') }}" data-hx-vi="Tạo tài khoản" data-hx-en="Create account">Tạo tài khoản</a>
                @endauth
                <a href="{{ url('/') }}#how-it-works" data-hx-vi="Hướng dẫn sử dụng" data-hx-en="User guide">Hướng dẫn sử dụng</a>
            </nav>

            <aside class="hx-f5-connect" aria-label="Kết nối với HEXAGON">
                <h3><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 9a10 10 0 0 1 14 0M8 12a6 6 0 0 1 8 0M11 15a2 2 0 0 1 2 0"/><circle cx="12" cy="19" r="1" fill="currentColor"/></svg><span data-hx-vi="Kết nối" data-hx-en="Connect">Kết nối</span></h3>
                <p data-hx-vi="Khám phá dịch vụ và quản lý thông tin của bạn dễ dàng trên HEXAGON." data-hx-en="Explore services and manage your information easily on HEXAGON.">Khám phá dịch vụ và quản lý thông tin của bạn dễ dàng trên HEXAGON.</p>
                <div class="hx-f5-shortcuts">
                    <a href="{{ url('/') }}#domain-search" aria-label="Tìm tên miền" title="Tìm tên miền">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg>
                    </a>
                    @if(\Illuminate\Support\Facades\Route::has('saas.index'))
                        <a href="{{ route('saas.index') }}" aria-label="Giải pháp SaaS" title="Giải pháp SaaS">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="8" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/><rect x="13" y="13" width="8" height="8" rx="2"/></svg>
                        </a>
                    @endif
                    <a href="{{ route('cart.index') }}" aria-label="Giỏ hàng" title="Giỏ hàng">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 2h3l3 14h13l2-10H5"/></svg>
                    </a>
                    <a href="{{ url('/') }}#support" aria-label="Trợ giúp" title="Trợ giúp">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M9.5 9.5a2.5 2.5 0 0 1 5 0c0 2-2.5 2-2.5 4M12 17h.01"/></svg>
                    </a>
                </div>
            </aside>
        </div>

        <div class="hx-f5-bottom">
            <span>© {{ date('Y') }} HEXAGON. <span data-hx-vi="Công ty Cổ phần Lục Giác. Bảo lưu mọi quyền." data-hx-en="Luc Giac Joint Stock Company. All rights reserved.">Công ty Cổ phần Lục Giác. Bảo lưu mọi quyền.</span></span>
            <nav aria-label="Liên kết cuối trang">
                <a href="{{ url('/') }}" data-hx-vi="Trang chủ" data-hx-en="Home">Trang chủ</a>
                <a href="{{ url('/') }}#pricing" data-hx-vi="Bảng giá" data-hx-en="Pricing">Bảng giá</a>
                <a href="{{ url('/') }}#support" data-hx-vi="Trợ giúp" data-hx-en="Help">Trợ giúp</a>
            </nav>
        </div>
    </div>
</footer>
