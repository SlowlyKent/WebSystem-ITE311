<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Upload Material</h3>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('materials/upload') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="course_id" value="<?= esc($course_id ?? '') ?>">
                        
                        <div class="mb-3">
                            <label for="file" class="form-label">Choose file</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".pdf,.ppt,.pptx" required>
                            <small class="form-text text-muted">
                                <strong>Allowed file types:</strong> PDF and PPT (PowerPoint) only. 
                                <br>Other file types (PNG, EXE, DOCX, etc.) are not allowed.
                            </small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload"></i> Upload
                            </button>
                            <a href="<?= base_url('materials/course/' . ($course_id ?? '')) ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Materials
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Client-side validation: Check file type before submitting
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file');
    const form = fileInput.closest('form');
    
    // List of allowed file extensions
    const allowedExtensions = ['pdf', 'ppt', 'pptx'];
    
    // When user selects a file, check if it's valid
    fileInput.addEventListener('change', function() {
        const fileName = this.files[0]?.name || '';
        const fileExtension = fileName.split('.').pop().toLowerCase();
        
        // Check if the file extension is in our allowed list
        if (fileName && !allowedExtensions.includes(fileExtension)) {
            alert('❌ Invalid file type!\n\nOnly PPT (PowerPoint) and PDF files are allowed.\n\nYour file: ' + fileName + '\nFile type: ' + fileExtension.toUpperCase() + '\n\nPlease select a PDF or PPT file.');
            // Clear the file input
            this.value = '';
        }
    });
    
    // Also validate when form is submitted (double check)
    form.addEventListener('submit', function(e) {
        const fileName = fileInput.files[0]?.name || '';
        const fileExtension = fileName.split('.').pop().toLowerCase();
        
        if (fileName && !allowedExtensions.includes(fileExtension)) {
            e.preventDefault(); // Stop form submission
            alert('❌ Invalid file type!\n\nOnly PPT (PowerPoint) and PDF files are allowed.\n\nPlease select a PDF or PPT file.');
            return false;
        }
    });
});
</script>
<?= $this->endSection() ?>