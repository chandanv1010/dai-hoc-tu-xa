<div class="table-of-contents">
    <div>
        <h3>Mục lục</h3>
        <button id="toggle-toc">Ẩn</button>
    </div>

    <ul id="toc-list" class="toc-list">
        @foreach ($items as $item)
            <li class="level-{{ $item['level'] }}">
                <a href="#{{ $item['id'] }}" class="toc-link">
                    {{ $item['numbering'] }}. {{ $item['text'] }}
                </a>
            </li>
        @endforeach
    </ul>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const btn = document.getElementById("toggle-toc");
        const list = document.getElementById("toc-list");

        btn.addEventListener("click", function() {
            if (list.style.display === "none") {
                list.style.display = "block";
                btn.textContent = "Ẩn";
            } else {
                list.style.display = "none";
                btn.textContent = "Hiện";
            }
        });

        // Smooth scroll (jQuery)
        $('a.toc-link').on('click', function(e) {
            e.preventDefault();
            let target = $(this).attr('href');
            $('html, body').animate({
                scrollTop: $(target).offset().top - 100
            }, 500);
        });
    });
</script>
