@if(isset($menu['main-menu']))
    <ul class="main-menu-list">
        {!! $menu['main-menu'] !!}
    </ul>
@else
    <ul class="main-menu-list">
        <li><a href="{{ write_url('gioi-thieu') }}" title="Giới thiệu">GIỚI THIỆU</a></li>
        <li><a href="{{ write_url('truong-dao-tao-tu-xa') }}" title="Trường đào tạo từ xa">TRƯỜNG ĐÀO TẠO TỪ XA</a></li>
        <li><a href="{{ write_url('nganh-dao-tao-tu-xa') }}" title="Ngành đào tạo từ xa">NGÀNH ĐÀO TẠO TỪ XA</a></li>
        <li><a href="{{ write_url('dao-tao-ngan-han') }}" title="Đào tạo ngắn hạn">ĐÀO TẠO NGẮN HẠN</a></li>
        <li><a href="{{ write_url('tin-tuc') }}" title="Tin tức">TIN TỨC</a></li>
        <li><a href="{{ write_url('lich-khai-giang') }}" title="Lịch khai giảng">LỊCH KHAI GIẢNG</a></li>
        <li><a href="{{ write_url('lien-he') }}" title="Liên hệ">LIÊN HỆ</a></li>
    </ul>
@endif