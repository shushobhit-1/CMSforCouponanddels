@extends('layouts.admin')

@section('title', 'Header Editor')

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .editor-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    
    .editor-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
    }
    
    .editor-tabs {
        border-bottom: 1px solid #dee2e6;
        background: #f8f9fa;
    }
    
    .nav-tabs .nav-link {
        border: none;
        border-radius: 0;
        color: #6c757d;
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        background: white;
        color: #007bff;
        border-bottom: 2px solid #007bff;
    }
    
    .preview-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        position: sticky;
        top: 20px;
    }
    
    .preview-header {
        background: #343a40;
        color: white;
        padding: 1rem;
        text-align: center;
    }
    
    .preview-content {
        min-height: 300px;
        padding: 2rem;
        border: 1px dashed #dee2e6;
        margin: 1rem;
        border-radius: 8px;
        background: #f8f9fa;
    }
    
    #htmlEditor {
        min-height: 400px;
        font-family: 'Monaco', 'Consolas', monospace;
        font-size: 14px;
    }
    
    .quill-editor {
        min-height: 300px;
    }
    
    .template-card {
        border: 2px solid #dee2e6;
        border-radius: 10px;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 1rem;
    }
    
    .template-card:hover {
        border-color: #007bff;
        background: #f8f9ff;
    }
    
    .template-preview {
        height: 80px;
        background: #f8f9fa;
        border-radius: 5px;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .settings-panel {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 2rem;
    }
    
    .shortcode-tag {
        background: #e9ecef;
        color: #495057;
        padding: 2px 6px;
        border-radius: 4px;
        font-family: monospace;
        font-size: 0.85rem;
        cursor: pointer;
        margin: 2px;
        display: inline-block;
    }
    
    .shortcode-tag:hover {
        background: #007bff;
        color: white;
    }
    
    .action-buttons {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1000;
    }
    
    .floating-btn {
        margin-left: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        border-radius: 25px;
        padding: 12px 24px;
        font-weight: bold;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Header Editor</h1>
            <p class="mb-0 text-muted">Customize your website header content and layout</p>
        </div>
        <div>
            <button class="btn btn-outline-secondary me-2" onclick="loadTemplate()">
                <i class="fas fa-download me-2"></i>Load Template
            </button>
            <button class="btn btn-info me-2" onclick="previewHeader()" target="_blank">
                <i class="fas fa-eye me-2"></i>Preview
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Editor Panel -->
        <div class="col-lg-8">
            <!-- Header Settings -->
            <div class="settings-panel">
                <h5 class="mb-3"><i class="fas fa-cog me-2"></i>Header Settings</h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Header Type</label>
                            <select class="form-select" id="headerType" onchange="updateHeaderType()">
                                <option value="default">Default Header</option>
                                <option value="centered">Centered Layout</option>
                                <option value="minimal">Minimal Header</option>
                                <option value="mega">Mega Menu Header</option>
                                <option value="custom">Custom HTML</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Background Color</label>
                            <input type="color" class="form-control form-control-color" id="headerBg" value="#ffffff">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Text Color</label>
                            <input type="color" class="form-control form-control-color" id="headerTextColor" value="#333333">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Header Height</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="headerHeight" value="80" min="60" max="150">
                                <span class="input-group-text">px</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="showLogo" checked>
                            <label class="form-check-label" for="showLogo">Show Logo</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="showSearch" checked>
                            <label class="form-check-label" for="showSearch">Show Search Bar</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="stickyHeader">
                            <label class="form-check-label" for="stickyHeader">Sticky Header</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Editor Container -->
            <div class="editor-container">
                <div class="editor-header">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Header Content Editor</h5>
                </div>
                
                <!-- Editor Tabs -->
                <div class="editor-tabs">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#visualEditor" role="tab">
                                <i class="fas fa-eye me-2"></i>Visual Editor
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#htmlEditor" role="tab">
                                <i class="fas fa-code me-2"></i>HTML Editor
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#templates" role="tab">
                                <i class="fas fa-layer-group me-2"></i>Templates
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#shortcodes" role="tab">
                                <i class="fas fa-tags me-2"></i>Shortcodes
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Tab Content -->
                <div class="tab-content p-3">
                    <!-- Visual Editor -->
                    <div class="tab-pane fade show active" id="visualEditor" role="tabpanel">
                        <div id="quillEditor" class="quill-editor"></div>
                    </div>
                    
                    <!-- HTML Editor -->
                    <div class="tab-pane fade" id="htmlEditor" role="tabpanel">
                        <textarea class="form-control" id="htmlContent" rows="20" placeholder="Enter your custom HTML code here...">{{ $headerHtml ?? '' }}</textarea>
                    </div>
                    
                    <!-- Templates -->
                    <div class="tab-pane fade" id="templates" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="template-card" onclick="loadHeaderTemplate('default')">
                                    <div class="template-preview">
                                        <div class="d-flex justify-content-between align-items-center w-100 px-2">
                                            <div class="fw-bold">LOGO</div>
                                            <div class="d-flex gap-2">
                                                <small>Home</small>
                                                <small>About</small>
                                                <small>Contact</small>
                                            </div>
                                        </div>
                                    </div>
                                    <h6>Default Header</h6>
                                    <small class="text-muted">Simple header with logo and navigation</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="template-card" onclick="loadHeaderTemplate('centered')">
                                    <div class="template-preview">
                                        <div class="text-center w-100">
                                            <div class="fw-bold mb-1">LOGO</div>
                                            <div class="d-flex justify-content-center gap-2">
                                                <small>Home</small>
                                                <small>About</small>
                                                <small>Contact</small>
                                            </div>
                                        </div>
                                    </div>
                                    <h6>Centered Header</h6>
                                    <small class="text-muted">Centered logo with navigation below</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="template-card" onclick="loadHeaderTemplate('promotional')">
                                    <div class="template-preview bg-warning">
                                        <small class="text-center">🔥 Special Offer - 50% OFF!</small>
                                    </div>
                                    <h6>Promotional Header</h6>
                                    <small class="text-muted">Header with promotional banner</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="template-card" onclick="loadHeaderTemplate('social')">
                                    <div class="template-preview">
                                        <div class="d-flex justify-content-between align-items-center w-100 px-2">
                                            <div class="fw-bold">LOGO</div>
                                            <div class="d-flex gap-1">
                                                <small>📧</small>
                                                <small>📱</small>
                                                <small>👥</small>
                                            </div>
                                        </div>
                                    </div>
                                    <h6>Social Header</h6>
                                    <small class="text-muted">Header with social media links</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Shortcodes -->
                    <div class="tab-pane fade" id="shortcodes" role="tabpanel">
                        <p class="text-muted mb-3">Click on any shortcode to insert it into your header content:</p>
                        
                        <h6>Basic Elements</h6>
                        <div class="mb-3">
                            <span class="shortcode-tag" onclick="insertShortcode('[site_name]')">[site_name]</span>
                            <span class="shortcode-tag" onclick="insertShortcode('[site_logo]')">[site_logo]</span>
                            <span class="shortcode-tag" onclick="insertShortcode('[search_bar]')">[search_bar]</span>
                            <span class="shortcode-tag" onclick="insertShortcode('[user_menu]')">[user_menu]</span>
                        </div>
                        
                        <h6>Navigation</h6>
                        <div class="mb-3">
                            <span class="shortcode-tag" onclick="insertShortcode('[main_menu]')">[main_menu]</span>
                            <span class="shortcode-tag" onclick="insertShortcode('[category_menu]')">[category_menu]</span>
                            <span class="shortcode-tag" onclick="insertShortcode('[breadcrumbs]')">[breadcrumbs]</span>
                        </div>
                        
                        <h6>Contact Information</h6>
                        <div class="mb-3">
                            <span class="shortcode-tag" onclick="insertShortcode('[contact_phone]')">[contact_phone]</span>
                            <span class="shortcode-tag" onclick="insertShortcode('[contact_email]')">[contact_email]</span>
                            <span class="shortcode-tag" onclick="insertShortcode('[social_links]')">[social_links]</span>
                        </div>
                        
                        <h6>Dynamic Content</h6>
                        <div class="mb-3">
                            <span class="shortcode-tag" onclick="insertShortcode('[current_date]')">[current_date]</span>
                            <span class="shortcode-tag" onclick="insertShortcode('[current_time]')">[current_time]</span>
                            <span class="shortcode-tag" onclick="insertShortcode('[user_name]')">[user_name]</span>
                            <span class="shortcode-tag" onclick="insertShortcode('[login_link]')">[login_link]</span>
                        </div>
                        
                        <h6>Special Elements</h6>
                        <div class="mb-3">
                            <span class="shortcode-tag" onclick="insertShortcode('[announcement_bar]')">[announcement_bar]</span>
                            <span class="shortcode-tag" onclick="insertShortcode('[language_switcher]')">[language_switcher]</span>
                            <span class="shortcode-tag" onclick="insertShortcode('[currency_switcher]')">[currency_switcher]</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Panel -->
        <div class="col-lg-4">
            <div class="preview-container">
                <div class="preview-header">
                    <h6 class="mb-0"><i class="fas fa-eye me-2"></i>Live Preview</h6>
                </div>
                
                <div class="preview-content" id="headerPreview">
                    <div class="text-center text-muted">
                        <i class="fas fa-eye fa-2x mb-2"></i>
                        <p>Preview will appear here as you edit</p>
                    </div>
                </div>
                
                <div class="p-3 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">Preview Mode:</small>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary active" onclick="setPreviewMode('desktop')">
                                <i class="fas fa-desktop"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setPreviewMode('tablet')">
                                <i class="fas fa-tablet-alt"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setPreviewMode('mobile')">
                                <i class="fas fa-mobile-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Action Buttons -->
<div class="action-buttons">
    <button class="btn btn-outline-danger floating-btn" onclick="resetHeader()">
        <i class="fas fa-trash me-2"></i>Reset
    </button>
    <button class="btn btn-success floating-btn" onclick="saveHeader()">
        <i class="fas fa-save me-2"></i>Save Header
    </button>
</div>
@endsection

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
// Initialize Quill editor
let quill;
let headerContent = `{{ $headerHtml ?? '' }}`;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Quill
    quill = new Quill('#quillEditor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'align': [] }],
                ['link', 'image'],
                ['clean']
            ]
        }
    });
    
    // Set initial content
    if (headerContent) {
        quill.root.innerHTML = headerContent;
        document.getElementById('htmlContent').value = headerContent;
    }
    
    // Sync editors
    quill.on('text-change', function() {
        document.getElementById('htmlContent').value = quill.root.innerHTML;
        updatePreview();
    });
    
    document.getElementById('htmlContent').addEventListener('input', function() {
        quill.root.innerHTML = this.value;
        updatePreview();
    });
    
    // Initial preview update
    updatePreview();
});

// Update preview
function updatePreview() {
    const content = document.getElementById('htmlContent').value;
    const preview = document.getElementById('headerPreview');
    
    preview.innerHTML = content || '<div class="text-center text-muted"><i class="fas fa-eye fa-2x mb-2"></i><p>Preview will appear here as you edit</p></div>';
}

// Insert shortcode
function insertShortcode(shortcode) {
    const range = quill.getSelection();
    if (range) {
        quill.insertText(range.index, shortcode);
    } else {
        quill.insertText(quill.getLength(), shortcode);
    }
}

// Load header template
function loadHeaderTemplate(template) {
    const templates = {
        default: `
            <div class="header-default d-flex justify-content-between align-items-center py-3">
                <div class="logo">
                    [site_logo] <strong>[site_name]</strong>
                </div>
                <div class="navigation">
                    [main_menu]
                </div>
                <div class="user-area">
                    [search_bar] [user_menu]
                </div>
            </div>
        `,
        centered: `
            <div class="header-centered text-center py-4">
                <div class="logo mb-3">
                    [site_logo]
                    <h2>[site_name]</h2>
                </div>
                <div class="navigation">
                    [main_menu]
                </div>
            </div>
        `,
        promotional: `
            <div class="promotional-bar bg-warning text-center py-2">
                <strong>🔥 Special Offer - Save up to 50% on all deals! Limited time only!</strong>
            </div>
            <div class="header-main d-flex justify-content-between align-items-center py-3">
                <div class="logo">
                    [site_logo] <strong>[site_name]</strong>
                </div>
                <div class="navigation">
                    [main_menu]
                </div>
                <div class="user-area">
                    [user_menu]
                </div>
            </div>
        `,
        social: `
            <div class="header-social d-flex justify-content-between align-items-center py-2 border-bottom">
                <div class="contact-info">
                    <small>[contact_email] | [contact_phone]</small>
                </div>
                <div class="social-links">
                    [social_links]
                </div>
            </div>
            <div class="header-main d-flex justify-content-between align-items-center py-3">
                <div class="logo">
                    [site_logo] <strong>[site_name]</strong>
                </div>
                <div class="navigation">
                    [main_menu]
                </div>
                <div class="search-area">
                    [search_bar]
                </div>
            </div>
        `
    };
    
    if (templates[template]) {
        quill.root.innerHTML = templates[template].trim();
        document.getElementById('htmlContent').value = templates[template].trim();
        updatePreview();
    }
}

// Save header
async function saveHeader() {
    const content = document.getElementById('htmlContent').value;
    
    try {
        const response = await fetch('/admin/appearance/header', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                header_html: content
            })
        });
        
        if (response.ok) {
            Swal.fire({
                icon: 'success',
                title: 'Header Saved!',
                text: 'Your header has been updated successfully',
                showConfirmButton: false,
                timer: 2000
            });
        } else {
            throw new Error('Failed to save header');
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Failed to save header'
        });
    }
}

// Reset header
function resetHeader() {
    Swal.fire({
        title: 'Reset Header?',
        text: 'This will clear all header content',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, reset it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            quill.root.innerHTML = '';
            document.getElementById('htmlContent').value = '';
            updatePreview();
        }
    });
}

// Preview header
function previewHeader() {
    window.open('/?preview_header=1', '_blank');
}

// Set preview mode
function setPreviewMode(mode) {
    const preview = document.getElementById('headerPreview');
    const buttons = document.querySelectorAll('.btn-group .btn');
    
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    switch(mode) {
        case 'tablet':
            preview.style.maxWidth = '768px';
            break;
        case 'mobile':
            preview.style.maxWidth = '375px';
            break;
        default:
            preview.style.maxWidth = '100%';
    }
}

// Update header type
function updateHeaderType() {
    const type = document.getElementById('headerType').value;
    if (type !== 'custom') {
        loadHeaderTemplate(type);
    }
}
</script>
@endpush