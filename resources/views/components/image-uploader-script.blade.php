<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('imageUploader', (existingCover = null, existingGallery = []) => ({
        coverPreview: existingCover,
        galleryImages: [], // array of { file, previewUrl, id }
        existingGallery: existingGallery, // array of { id, url }
        
        async handleCoverChange(event) {
            const file = event.target.files[0];
            if (file) {
                // Compress file before preview and upload
                const compressedFile = await this.compressImage(file, 1200, 1200, 0.75);
                this.coverPreview = URL.createObjectURL(compressedFile);
                
                // Replace the file in the input using DataTransfer
                const dt = new DataTransfer();
                dt.items.add(compressedFile);
                event.target.files = dt.files;
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

        async handleGalleryChange(event) {
            const files = Array.from(event.target.files);
            
            // Tampilkan SweetAlert Loading
            Swal.fire({
                title: 'Mengompres Foto...',
                text: 'Harap tunggu, foto sedang dikecilkan ukurannya.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            for (const file of files) {
                const totalImages = this.galleryImages.length + this.existingGallery.length;
                if (totalImages < 18) {
                    const compressedFile = await this.compressImage(file, 1200, 1200, 0.75);
                    this.galleryImages.push({
                        file: compressedFile,
                        previewUrl: URL.createObjectURL(compressedFile),
                        id: Date.now() + Math.random()
                    });
                } else {
                    Swal.fire('Batas Maksimal', 'Maksimal 18 foto diperbolehkan.', 'warning');
                    break;
                }
            }
            
            this.syncGalleryInput();
            Swal.close();
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
        },
        
        compressImage(file, maxWidth = 1200, maxHeight = 1200, quality = 0.75) {
            return new Promise((resolve) => {
                // Jangan kompres jika ukuran sudah kecil (misal di bawah 500KB) atau jika file bukan gambar
                if (!file.type.startsWith('image/') || file.size < 500000) {
                    resolve(file);
                    return;
                }

                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = (event) => {
                    const img = new Image();
                    img.src = event.target.result;
                    img.onload = () => {
                        let width = img.width;
                        let height = img.height;

                        if (width > maxWidth || height > maxHeight) {
                            if (width > height) {
                                height = Math.round((height *= maxWidth / width));
                                width = maxWidth;
                            } else {
                                width = Math.round((width *= maxHeight / height));
                                height = maxHeight;
                            }
                        }

                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);
                        
                        canvas.toBlob((blob) => {
                            const newFile = new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            });
                            resolve(newFile);
                        }, 'image/jpeg', quality);
                    };
                };
            });
        }
    }));
});
</script>
