// invoice_upload.js - Custom JS for invoice upload page
var invoiceId = window.invoiceId;
Dropzone.autoDiscover = false;
var invoiceDropzone = new Dropzone("#invoiceDropzone", {
    url: window.invoiceUploadUrl,
    paramName: 'invoice_file',
    autoProcessQueue: false,
    acceptedFiles: 'application/pdf',
    maxFilesize: 5,
    maxFiles: 1,
    init: function() {
        var dz = this;
        var progressBar = document.getElementById('uploadProgressBar');
        dz.on('maxfilesexceeded', function(file) {
            dz.removeAllFiles();
            dz.addFile(file);
        });
        document.getElementById('startUpload').addEventListener('click', function() {
            if (dz.getQueuedFiles().length) dz.processQueue();
            else alert('Vui lòng chọn file để upload.');
        });
        dz.on('uploadprogress', function(file, progress) {
            progressBar.style.width = progress + '%';
        });
        dz.on('success', function(file, res) {
            try { res = (typeof res === 'string') ? JSON.parse(res) : res; } catch(e) { res = {success:false}; }
            if (res.success) {
                window.location.href = window.invoiceReviewUrl + invoiceId;
            } else {
                alert('Lỗi: ' + (res.message||'Đã xảy ra lỗi.'));
            }
        });
        dz.on('error', function(file, err) {
            alert('Lỗi upload: ' + err);
        });
    }
});
