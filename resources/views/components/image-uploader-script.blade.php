<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('imageUploader', (existingCover = null, existingGallery = []) => ({
        coverPreview: existingCover,
        galleryImages: [], // array of { file, previewUrl, id }
        existingGallery: existingGallery, // array of { id, url }
        
        handleCoverChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.coverPreview = URL.createObjectURL(file);
            } else {
                this.coverPreview = existingCover; // fallback to original if cancelled
            }
        },

        removeCoverImage() {
            this.coverPreview = null;
            if (this.$refs.coverInput) {
                this.$refs.coverInput.value = '';
            }
        },

        handleGalleryChange(event) {
            const files = Array.from(event.target.files);
            
            files.forEach(file => {
                const totalImages = this.galleryImages.length + this.existingGallery.length;
                if (totalImages < 18) {
                    this.galleryImages.push({
                        file: file,
                        previewUrl: URL.createObjectURL(file),
                        id: Date.now() + Math.random()
                    });
                } else {
                    Swal.fire('Batas Maksimal', 'Maksimal 18 foto diperbolehkan.', 'warning');
                }
            });
            
            this.syncGalleryInput();
        },

        removeGalleryImage(id) {
            this.galleryImages = this.galleryImages.filter(img => img.id !== id);
            this.syncGalleryInput();
        },

        removeExistingImage(id) {
            Swal.fire({
                title: 'Hapus Foto?',
                text: 'Yakin ingin menghapus foto ini? (Akan terhapus permanen saat disimpan)',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Add a hidden input to mark this image for deletion
                    let hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'delete_images[]';
                    hidden.value = id;
                    this.$root.appendChild(hidden);
                    
                    this.existingGallery = this.existingGallery.filter(img => img.id !== id);
                }
            });
        },

        syncGalleryInput() {
            const dataTransfer = new DataTransfer();
            this.galleryImages.forEach(img => {
                dataTransfer.items.add(img.file);
            });
            this.$refs.galleryInput.files = dataTransfer.files;
        }
    }));
});
</script>
