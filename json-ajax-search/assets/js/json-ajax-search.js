(function($) {
    $(document).ready(function() {
        var $form = $('#json-ajax-search-form');
        var $results = $('#json-ajax-search-results');
        var $pagination = $('#json-ajax-search-pagination');

        function loadResults(page = 1) {
            var formData = $form.serialize();
            formData += '&action=json_ajax_search&nonce=' + json_ajax_search_params.nonce + '&page=' + page;

            $.ajax({
                url: json_ajax_search_params.ajax_url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    console.log('AJAX Response:', response); // Debug: Log the entire response
                    if (response.success && response.data) {
                        displayResults(response.data.data);
                        displayPagination(response.data.data.total_pages, response.data.data.current_page);
                        console.log('Total items:', response.data.data); // Debug: Log total items
                    } else {
                        displayError("Error: Invalid response from server.");
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error:', textStatus, errorThrown); // Debug: Log AJAX errors
                    displayError("Error: Could not connect to the server.");
                }
            });
        }

        function displayResults(data) {
            if (!Array.isArray(data) || data.length === 0) {
                $results.html('<p>No results found.</p>');
                return;
            }

            var html = '<table><thead><tr><th>Nme</th><th>Surname 1</th><th>Surname 2</th><th>Email</th></tr></thead><tbody>';
            
            data.forEach(function(item) {
                html += '<tr>';
                html += '<td>' + (item.name || '') + '</td>';
                html += '<td>' + (item.surname1 || '') + '</td>';
                html += '<td>' + (item.surname2 || '') + '</td>';
                html += '<td>' + (item.email || '') + '</td>';
                html += '</tr>';
            });

            html += '</tbody></table>';
            $results.html(html);
        }

        function displayPagination(totalPages, currentPage) {
            if (!totalPages || totalPages <= 1) {
                $pagination.empty();
                return;
            }

            var html = '';
            for (var i = 1; i <= totalPages; i++) {
                html += '<a href="#" class="page-number' + (i === currentPage ? ' current' : '') + '" data-page="' + i + '">' + i + '</a>';
            }
            $pagination.html(html);
        }

        function displayError(message) {
            $results.html('<p class="error">' + message + '</p>');
            $pagination.empty();
        }

        $form.on('submit', function(e) {
            e.preventDefault();
            loadResults();
        });

        $pagination.on('click', '.page-number', function(e) {
            e.preventDefault();
            var page = $(this).data('page');
            loadResults(page);
        });

        // Load initial results
        loadResults();
    });
})(jQuery);
