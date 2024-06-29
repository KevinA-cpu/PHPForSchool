<?php
include 'base.php';
include 'database.php';
?>

<div class="container">
    <h1>Tìm kiếm bài báo</h1>

    <form id="searchForm" class="mb-3">
        <div class="input-group">
            <input type="text" class="form-control" name="keyword" placeholder="Tìm kiếm bài báo theo từ khóa">
            <button class="btn btn-primary" type="submit">Tìm kiếm</button>
        </div>
    </form>

    <div id="results">
        <!-- Results will be loaded here -->
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        function loadResults(page = 1) {
            const keyword = $('input[name="keyword"]').val();
            $.ajax({
                url: 'search.php',
                method: 'POST',
                data: {
                    keyword: keyword,
                    page: page
                },
                success: function(data) {
                    $('#results').html(data);
                }
            });
        }

        $('#searchForm').on('submit', function(event) {
            event.preventDefault();
            loadResults();
        });

        $(document).on('click', '.pagination a', function(event) {
            event.preventDefault();
            const page = $(this).data('page');
            loadResults(page);
        });

        // Load initial results
        loadResults();
    });
</script>