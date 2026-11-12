<section class="hx-saas-inquiry" id="tu-van" aria-labelledby="hx-saas-inquiry-title">
    <div class="hx-saas-inquiry-info">
        <span class="hx-saas-inquiry-eyebrow">HEXAGON · SaaS</span>
        <h2 id="hx-saas-inquiry-title" data-saas-vi="Nhận tư vấn về dịch vụ" data-saas-en="Request a consultation">Nhận tư vấn về dịch vụ</h2>
        <p data-saas-vi="Để lại thông tin và nhu cầu của bạn. Chúng tôi sẽ liên hệ để trao đổi về giải pháp phù hợp."
           data-saas-en="Share your contact details and requirements. Our team will reach out to discuss a suitable solution.">Để lại thông tin và nhu cầu của bạn. Chúng tôi sẽ liên hệ để trao đổi về giải pháp phù hợp.</p>
        <p class="hx-saas-inquiry-selected"><strong data-saas-vi="Dịch vụ:" data-saas-en="Service:">Dịch vụ:</strong> <span data-saas-vi="{{ $service->name_vi }}" data-saas-en="{{ $service->name_en ?: $service->name_vi }}">{{ $service->name_vi }}</span></p>
    </div>
    <div class="hx-saas-inquiry-panel">
        @if(session('saas_inquiry_success'))
            <div class="hx-saas-inquiry-alert success" role="status"
                 data-saas-vi="{{ session('saas_inquiry_success') }}"
                 data-saas-en="Your request has been received. Our team will contact you soon.">{{ session('saas_inquiry_success') }}</div>
        @endif
        @if($errors->any())
            <div class="hx-saas-inquiry-alert error" role="alert">
                <strong data-saas-vi="Vui lòng kiểm tra thông tin:" data-saas-en="Please check your details:">Vui lòng kiểm tra thông tin:</strong>
                <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        <form action="{{ route('saas.inquiries.store', ['slug' => $service->slug]) }}" method="POST" class="hx-saas-inquiry-form">
            @csrf
            <div class="hx-saas-inquiry-trap" aria-hidden="true">
                <label for="hx-inquiry-website">Địa chỉ website</label><input id="hx-inquiry-website" type="text" name="website" tabindex="-1" autocomplete="off" value="">
            </div>
            <div class="hx-saas-inquiry-fields">
                <label><span data-saas-vi="Họ và tên *" data-saas-en="Full name *">Họ và tên *</span><input name="customer_name" value="{{ old('customer_name') }}" autocomplete="name" maxlength="150" required placeholder="Nguyễn Văn A"></label>
                <label><span data-saas-vi="Email *" data-saas-en="Email *">Email *</span><input name="customer_email" type="email" value="{{ old('customer_email') }}" autocomplete="email" maxlength="190" required placeholder="email@example.com"></label>
                <label><span data-saas-vi="Số điện thoại *" data-saas-en="Phone number *">Số điện thoại *</span><input name="customer_phone" type="tel" value="{{ old('customer_phone') }}" autocomplete="tel" maxlength="30" required placeholder="09xx xxx xxx"></label>
            </div>
            <label class="hx-saas-inquiry-message"><span data-saas-vi="Nhu cầu của bạn (không bắt buộc)" data-saas-en="Your requirements (optional)">Nhu cầu của bạn (không bắt buộc)</span><textarea name="message" rows="4" maxlength="2000" placeholder="Mô tả nhu cầu, số lượng người dùng, thời gian dự kiến...">{{ old('message') }}</textarea></label>
            <label class="hx-saas-inquiry-consent"><input type="checkbox" name="consent" value="1" @checked(old('consent')) required><span data-saas-vi="Tôi đồng ý để HEXAGON sử dụng thông tin trên nhằm liên hệ tư vấn dịch vụ này." data-saas-en="I agree that HEXAGON may use these details to contact me about this service.">Tôi đồng ý để HEXAGON sử dụng thông tin trên nhằm liên hệ tư vấn dịch vụ này.</span></label>
            <button class="hx-saas-inquiry-submit" type="submit"><span data-saas-vi="Gửi yêu cầu tư vấn" data-saas-en="Send consultation request">Gửi yêu cầu tư vấn</span> <span aria-hidden="true">→</span></button>
        </form>
    </div>
</section>
