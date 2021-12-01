<script type="text/javascript">
    $(document).ready(function () {
        var filter = [];
        var sort = [];
        var radios = [];
        $.ajaxSetup({
            type: 'get',
            dataType: 'html',
            url: '',
        });

        function checkboxes() {
            $('.filterShow').click(function () {
                filter = [];
                $('.filterChecked').each(function () {
                    if ($(this).is(":checked")) {
                        filter.push($(this).val());
                    }
                });
                return $.ajax({
                    data: {subFilter: filter},
                    success:
                        function (data) {
                            data = $(data).find('div#productData');
                            $('#productFind').html(data);
                        }
                });
            });
        }

        checkboxes();


        function selectSort() {
            $('#sortSelect').change(function () {
                sort = $(this).val();
                return $.ajax({
                    data: {sort: sort, subFilter: filter},
                    success:
                        function (data) {
                            data = $(data).find('div#productData');
                            $('#productFind').html(data);
                        }
                });
            });
        }

        function radioSelected() {
            $('.radioShow').change(function () {
                radios = $(this).val();
                return $.ajax({
                    data: {radios: radios, sort: sort, subFilter: filter},

                    success:
                        function (data) {
                            data = $(data).find('div#productData');
                            $('#productFind').html(data);
                            if(radios=='all'){
                                $(".wrapper__cataloglimit-active").text('Всё');
                            }
                            else {
                                $(".wrapper__cataloglimit-active").text(radios);
                            }
                        }
                });
            });
        }

        $.ajax().then(checkboxes).then(selectSort).then(radioSelected).then(gridAttrFun).then(listAttrFun).then(cardAttrFun);
    });
</script>

