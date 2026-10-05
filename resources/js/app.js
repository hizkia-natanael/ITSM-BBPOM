import './bootstrap';

import Alpine from 'alpinejs';
import { fileDropzoneUploader } from './file-dropzone-uploader';

window.Alpine = Alpine;
window.fileDropzoneUploader = fileDropzoneUploader;
Alpine.data('fileDropzoneUploader', fileDropzoneUploader);

Alpine.start();

