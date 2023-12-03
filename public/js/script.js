$( document ).ready(function() {
    $('.download-button').click(function() {

        $.ajax({
            url: '/getCsv',
            type: 'POST',
            data: {
                filePath: $(this).data('file-path')
            },
            success: function(response) {
                downloadFile(response.name);
            }
        });
    });

    $('.delete-button').click(function() {
        var btnClicked = $(this);
        var filePath = $(this).data('file-path');

        $.ajax({
            url: '/deleteFile',
            type: 'POST',
            data: { filePath: filePath },
            success: function(response) {
                btnClicked.closest('tr').remove();
            }
        });
    });

    $('.check-button').click(function(e) {
        e.preventDefault();
        var button = $(this);
        var fileId = button.data('file-path');
        $.ajax({
            url: '/checkFile',
            type: 'POST',
            data: { fileId: fileId },
            success: function(data) {
                console.log(data);
                button.closest('tr').find('.treatment-status').text(data.file.treated + ' / ' + data.file.total);
            }
        });
    });
});

function downloadFile(name) {
    var link = document.createElement('a');
    var nameToDl = name.concat(".csv");
    var pathForDl = '/files/output/';
    link.download = nameToDl;
    link.href = pathForDl.concat(nameToDl);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

