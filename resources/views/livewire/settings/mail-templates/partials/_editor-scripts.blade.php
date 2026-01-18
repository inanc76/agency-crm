{{--
    🛡️ ZIRHLI BELGELEME KARTI (V12.2)
    -------------------------------------------------------------------------
    PARTIAL    : Editör Kaynakları (_editor-scripts.blade.php)
    SORUMLULUK : Quill kütüphanesinin yüklenmesi, stillendirilmesi ve 
                 layout stack'lerine (styles/scripts) güvenli pushlanması.
    
    ZIRH PROTOKOLÜ:
    - Quill.js CDN (Safe Loading)
    - Custom Snow Theme Styles
    - Alpine.js Integration Bridge (Safe Initialization)
    -------------------------------------------------------------------------
--}}

@push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .ql-toolbar.ql-snow {
            background-color: #f8fafc;
            border: none !important;
            border-bottom: 1px solid var(--input-border) !important;
        }
        .ql-container.ql-snow {
            border: none !important;
            background-color: white;
        }
        .ql-editor {
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #1e293b;
            background-color: white !important;
            min-height: 700px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        function initializeQuillEditor(element, content, onUpdate, onEvent) {
            // Cleanup any previous Quill toolbars/containers that might linger
            const existingToolbars = element.parentElement.querySelectorAll('.ql-toolbar');
            existingToolbars.forEach(el => el.remove());
            
            const editorContainer = element.querySelector('#quill-editor');
            if (editorContainer) {
                editorContainer.innerHTML = '';
            }

            const quill = new Quill('#quill-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        ['link', 'blockquote', 'code-block', 'image'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'color': [] }, { 'background': [] }],
                        ['clean']
                    ]
                }
            });

            if (content) {
                quill.clipboard.dangerouslyPasteHTML(content);
            }

            quill.on('text-change', () => {
                onUpdate(quill.root.innerHTML);
            });

            if (onEvent) {
                onEvent(quill);
            }
            
            return quill;
        }
    </script>
@endpush