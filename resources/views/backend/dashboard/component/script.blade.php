<!-- Mainly scripts -->
<script src="backend/js/bootstrap.min.js"></script>
<script src="backend/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="backend/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="backend/plugins/jquery-ui.js"></script>



<script src="backend/js/inspinia.js"></script>
{{-- <script src="backend/js/plugins/pace/pace.min.js"></script> --}}
<!-- jQuery UI -->
<script src="backend/js/plugins/toastr/toastr.min.js"></script>
@if(isset($config['js']) && is_array($config['js']))
    @foreach($config['js'] as $key => $val)
        {!! '<script src="'.$val.'"></script>' !!}
    @endforeach
@endif

<script src="backend/library/library.js?v={{ time() }}"></script>

@if(isset($template) && strpos($template, 'post.store') !== false)
<script>
$(document).ready(function() {
    // Setup Select2 cho tags với khả năng tạo tag mới
    if($('#post-tags').length){
        $('#post-tags').select2({
            tags: true,
            tokenSeparators: [','],
            placeholder: 'Chọn hoặc nhập tag mới',
            allowClear: true,
            ajax: {
                url: $('#post-tags').data('tags-url'),
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function(tag) {
                            return {
                                id: tag.name,
                                text: tag.name
                            };
                        })
                    };
                },
                cache: true
            },
            createTag: function (params) {
                var term = $.trim(params.term);
                if (term === '') {
                    return null;
                }
                return {
                    id: term,
                    text: term,
                    newTag: true
                };
            }
        });
    }
});
</script>
@endif