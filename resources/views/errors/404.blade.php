@extends('frontend.homepage.layout')

@section('content')
<style>
    .error-404-page {
        min-height: 60vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #008DC2 0%, #00B9FF 100%);
        padding: 40px 20px;
        position: relative;
        overflow: hidden;
        margin: 40px 0;
    }
    
    .error-404-page::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
    }
    
    .error-404-container {
        max-width: 800px;
        width: 100%;
        text-align: center;
        position: relative;
        z-index: 1;
    }
    
    .error-404-content {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        padding: 60px 40px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(10px);
    }
    
    .error-404-number {
        font-size: 150px;
        font-weight: 900;
        background: linear-gradient(135deg, #008DC2 0%, #00B9FF 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
        margin-bottom: 20px;
        font-family: 'Asap', sans-serif;
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    
    .error-404-title {
        font-size: 36px;
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
        font-family: 'Asap', sans-serif;
    }
    
    .error-404-description {
        font-size: 18px;
        color: #666;
        margin-bottom: 40px;
        line-height: 1.6;
        font-family: 'Quicksand', sans-serif;
    }
    
    .error-404-actions {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .btn-404 {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 15px 30px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        font-size: 16px;
        transition: all 0.3s ease;
        font-family: 'Quicksand', sans-serif;
        border: 2px solid transparent;
    }
    
    .btn-404-primary {
        background: linear-gradient(135deg, #008DC2 0%, #00B9FF 100%);
        color: #fff;
        box-shadow: 0 4px 15px rgba(0, 141, 194, 0.4);
    }
    
    .btn-404-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 141, 194, 0.6);
        color: #fff;
    }
    
    .btn-404-secondary {
        background: #fff;
        color: #008DC2;
        border-color: #008DC2;
    }
    
    .btn-404-secondary:hover {
        background: #008DC2;
        color: #fff;
        transform: translateY(-2px);
    }
    
    .error-404-icon {
        font-size: 24px;
    }
    
    @media (max-width: 768px) {
        .error-404-number {
            font-size: 100px;
        }
        
        .error-404-title {
            font-size: 28px;
        }
        
        .error-404-description {
            font-size: 16px;
        }
        
        .error-404-content {
            padding: 40px 30px;
        }
        
        .error-404-actions {
            flex-direction: column;
        }
        
        .btn-404 {
            width: 100%;
            justify-content: center;
        }
    }
    
    .error-404-search {
        margin-top: 30px;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .error-404-search-form {
        display: flex;
        gap: 10px;
    }
    
    .error-404-search-input {
        flex: 1;
        padding: 12px 20px;
        border: 2px solid #e0e0e0;
        border-radius: 50px;
        font-size: 16px;
        outline: none;
        transition: all 0.3s ease;
        font-family: 'Quicksand', sans-serif;
    }
    
    .error-404-search-input:focus {
        border-color: #008DC2;
        box-shadow: 0 0 0 3px rgba(0, 141, 194, 0.1);
    }
    
    .error-404-search-btn {
        padding: 12px 25px;
        background: linear-gradient(135deg, #008DC2 0%, #00B9FF 100%);
        color: #fff;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
        font-family: 'Quicksand', sans-serif;
    }
    
    .error-404-search-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 141, 194, 0.4);
    }
</style>

<div class="error-404-page">
    <div class="error-404-container">
        <div class="error-404-content">
            <div class="error-404-number">404</div>
            <h1 class="error-404-title">Trang không tìm thấy</h1>
            <p class="error-404-description">
                Xin lỗi, trang bạn đang tìm kiếm không tồn tại hoặc đã bị xóa.<br>
                Vui lòng kiểm tra lại đường dẫn hoặc quay về trang chủ.
            </p>
            
            <div class="error-404-actions">
                <a href="{{ route('home.index') }}" class="btn-404 btn-404-primary">
                    <i class="fa fa-home error-404-icon"></i>
                    Về trang chủ
                </a>
                <a href="javascript:history.back()" class="btn-404 btn-404-secondary">
                    <i class="fa fa-arrow-left error-404-icon"></i>
                    Quay lại
                </a>
            </div>
            
            <div class="error-404-search">
                <form class="error-404-search-form" action="{{ route('product.catalogue.search') }}" method="GET">
                    <input 
                        type="text" 
                        name="keyword" 
                        class="error-404-search-input" 
                        placeholder="Tìm kiếm khóa học, ngành học..."
                        required
                    >
                    <button type="submit" class="error-404-search-btn">
                        <i class="fa fa-search"></i> Tìm kiếm
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
