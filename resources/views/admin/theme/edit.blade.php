@extends('layouts.admin')

@section('title', 'Theme Customization')

@push('styles')
<style>
    .theme-preview {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        position: sticky;
        top: 20px;
    }
    
    .color-picker-wrapper {
        position: relative;
        display: inline-block;
        width: 100px;
        height: 40px;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid #dee2e6;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .color-picker-wrapper:hover {
        border-color: #007bff;
        transform: scale(1.05);
    }
    
    .color-picker {
        width: 100%;
        height: 100%;
        border: none;
        cursor: pointer;
    }
    
    .font-preview {
        padding: 1rem;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .font-preview:hover,
    .font-preview.selected {
        border-color: #007bff;
        background: #f8f9ff;
    }
    
    .preview-section {
        margin-bottom: 2rem;
        padding: 1.5rem;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        background: #f8f9fa;
    }
    
    .customization-panel {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 2rem;
    }
    
    .section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem 1.5rem;
        margin: -2rem -2rem 2rem -2rem;
        border-radius: 15px 15px 0 0;
    }
    
    .preset-themes {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .preset-theme {
        border: 2px solid #dee2e6;
        border-radius: 10px;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }
    
    .preset-theme:hover,
    .preset-theme.active {
        border-color: #007bff;
        background: #f8f9ff;
    }
    
    .preset-colors {
        display: flex;
        gap: 5px;
        justify-content: center;
        margin-bottom: 0.5rem;
    }
    
    .preset-color {
        width: 20px;
        height: 20px;
        border-radius: 50%;
    }
    
    .range-slider {
        width: 100%;
        height: 6px;
        border-radius: 3px;
        background: #dee2e6;
        outline: none;
        -webkit-appearance: none;
    }
    
    .range-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #007bff;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
    
    .css-editor {
        font-family: 'Monaco', 'Consolas', monospace;
        font-size: 14px;
        line-height: 1.5;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        resize: vertical;
    }
    
    .live-preview {
        border: 1px solid #dee2e6;
        border-radius: 10px;
        overflow: hidden;
        background: white;
    }
    
    .preview-header {
        background: var(--primary-color, #007bff);
        color: white;
        padding: 1rem;
    }
    
    .preview-content {
        padding: 1.5rem;
    }
    
    .preview-button {
        background: var(--primary-color, #007bff);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: var(--border-radius, 4px);
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }
    
    .preview-card {
        border: 1px solid #dee2e6;
        border-radius: var(--border-radius, 4px);
        padding: 1rem;
        margin-bottom: 1rem;
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
            <h1 class="h3 mb-0 text-gray-800">Theme Customization</h1>
            <p class="mb-0 text-muted">Customize colors, fonts, and styling for your website</p>
        </div>
        <div>
            <button class="btn btn-outline-secondary me-2" onclick="resetToDefault()">
                <i class="fas fa-undo me-2"></i>Reset to Default
            </button>
            <button class="btn btn-info me-2" onclick="previewChanges()">
                <i class="fas fa-eye me-2"></i>Preview
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Customization Panels -->
        <div class="col-lg-8">
            <!-- Preset Themes -->
            <div class="customization-panel">
                <div class="section-header">
                    <h5 class="mb-0"><i class="fas fa-palette me-2"></i>Preset Themes</h5>
                </div>
                
                <div class="preset-themes">
                    <div class="preset-theme" onclick="applyPreset('default')">
                        <div class="preset-colors">
                            <div class="preset-color" style="background: #007bff;"></div>
                            <div class="preset-color" style="background: #6c757d;"></div>
                            <div class="preset-color" style="background: #28a745;"></div>
                        </div>
                        <h6>Default</h6>
                        <small class="text-muted">Classic blue theme</small>
                    </div>
                    
                    <div class="preset-theme" onclick="applyPreset('modern')">
                        <div class="preset-colors">
                            <div class="preset-color" style="background: #6f42c1;"></div>
                            <div class="preset-color" style="background: #e83e8c;"></div>
                            <div class="preset-color" style="background: #fd7e14;"></div>
                        </div>
                        <h6>Modern</h6>
                        <small class="text-muted">Purple gradient</small>
                    </div>
                    
                    <div class="preset-theme" onclick="applyPreset('nature')">
                        <div class="preset-colors">
                            <div class="preset-color" style="background: #28a745;"></div>
                            <div class="preset-color" style="background: #20c997;"></div>
                            <div class="preset-color" style="background: #17a2b8;"></div>
                        </div>
                        <h6>Nature</h6>
                        <small class="text-muted">Green & teal</small>
                    </div>
                    
                    <div class="preset-theme" onclick="applyPreset('sunset')">
                        <div class="preset-colors">
                            <div class="preset-color" style="background: #fd7e14;"></div>
                            <div class="preset-color" style="background: #dc3545;"></div>
                            <div class="preset-color" style="background: #ffc107;"></div>
                        </div>
                        <h6>Sunset</h6>
                        <small class="text-muted">Orange & red</small>
                    </div>
                </div>
            </div>

            <!-- Color Customization -->
            <div class="customization-panel">
                <div class="section-header">
                    <h5 class="mb-0"><i class="fas fa-paint-brush me-2"></i>Colors</h5>
                </div>
                
                <form id="themeForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Primary Color</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="color-picker-wrapper">
                                        <input type="color" class="color-picker" name="theme_primary_color" 
                                               value="{{ $settings['theme_primary_color'] ?? '#007bff' }}" 
                                               onchange="updatePreview()">
                                    </div>
                                    <input type="text" class="form-control" style="max-width: 120px;" 
                                           value="{{ $settings['theme_primary_color'] ?? '#007bff' }}" readonly>
                                </div>
                                <small class="text-muted">Main brand color for buttons, links, etc.</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Secondary Color</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="color-picker-wrapper">
                                        <input type="color" class="color-picker" name="theme_secondary_color" 
                                               value="{{ $settings['theme_secondary_color'] ?? '#6c757d' }}" 
                                               onchange="updatePreview()">
                                    </div>
                                    <input type="text" class="form-control" style="max-width: 120px;" 
                                           value="{{ $settings['theme_secondary_color'] ?? '#6c757d' }}" readonly>
                                </div>
                                <small class="text-muted">Secondary accent color</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Success Color</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="color-picker-wrapper">
                                        <input type="color" class="color-picker" name="theme_success_color" 
                                               value="{{ $settings['theme_success_color'] ?? '#28a745' }}" 
                                               onchange="updatePreview()">
                                    </div>
                                    <input type="text" class="form-control" style="max-width: 120px;" 
                                           value="{{ $settings['theme_success_color'] ?? '#28a745' }}" readonly>
                                </div>
                                <small class="text-muted">Success messages and positive actions</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Accent Color</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="color-picker-wrapper">
                                        <input type="color" class="color-picker" name="theme_accent_color" 
                                               value="{{ $settings['theme_accent_color'] ?? '#ffc107' }}" 
                                               onchange="updatePreview()">
                                    </div>
                                    <input type="text" class="form-control" style="max-width: 120px;" 
                                           value="{{ $settings['theme_accent_color'] ?? '#ffc107' }}" readonly>
                                </div>
                                <small class="text-muted">Highlights and special elements</small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Typography -->
            <div class="customization-panel">
                <div class="section-header">
                    <h5 class="mb-0"><i class="fas fa-font me-2"></i>Typography</h5>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Body Font</label>
                            <select class="form-select" name="theme_font_family" onchange="updatePreview()">
                                <option value="Inter, sans-serif" {{ ($settings['theme_font_family'] ?? '') == 'Inter, sans-serif' ? 'selected' : '' }}>Inter</option>
                                <option value="Roboto, sans-serif" {{ ($settings['theme_font_family'] ?? '') == 'Roboto, sans-serif' ? 'selected' : '' }}>Roboto</option>
                                <option value="Open Sans, sans-serif" {{ ($settings['theme_font_family'] ?? '') == 'Open Sans, sans-serif' ? 'selected' : '' }}>Open Sans</option>
                                <option value="Poppins, sans-serif" {{ ($settings['theme_font_family'] ?? '') == 'Poppins, sans-serif' ? 'selected' : '' }}>Poppins</option>
                                <option value="Montserrat, sans-serif" {{ ($settings['theme_font_family'] ?? '') == 'Montserrat, sans-serif' ? 'selected' : '' }}>Montserrat</option>
                                <option value="Lato, sans-serif" {{ ($settings['theme_font_family'] ?? '') == 'Lato, sans-serif' ? 'selected' : '' }}>Lato</option>
                            </select>
                            
                            <div class="font-preview mt-2" style="font-family: Inter, sans-serif;">
                                <p class="mb-1">The quick brown fox jumps over the lazy dog</p>
                                <small class="text-muted">1234567890 !@#$%^&*()</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Heading Font</label>
                            <select class="form-select" name="theme_heading_font" onchange="updatePreview()">
                                <option value="Inter, sans-serif" {{ ($settings['theme_heading_font'] ?? '') == 'Inter, sans-serif' ? 'selected' : '' }}>Inter</option>
                                <option value="Roboto, sans-serif" {{ ($settings['theme_heading_font'] ?? '') == 'Roboto, sans-serif' ? 'selected' : '' }}>Roboto</option>
                                <option value="Playfair Display, serif" {{ ($settings['theme_heading_font'] ?? '') == 'Playfair Display, serif' ? 'selected' : '' }}>Playfair Display</option>
                                <option value="Merriweather, serif" {{ ($settings['theme_heading_font'] ?? '') == 'Merriweather, serif' ? 'selected' : '' }}>Merriweather</option>
                                <option value="Oswald, sans-serif" {{ ($settings['theme_heading_font'] ?? '') == 'Oswald, sans-serif' ? 'selected' : '' }}>Oswald</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Font Size</label>
                            <div class="d-flex align-items-center gap-3">
                                <input type="range" class="range-slider" name="theme_font_size" 
                                       min="12" max="20" value="{{ $settings['theme_font_size'] ?? '16' }}" 
                                       onchange="updatePreview(); this.nextElementSibling.textContent = this.value + 'px'">
                                <span class="fw-bold">{{ $settings['theme_font_size'] ?? '16' }}px</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Border Radius</label>
                            <div class="d-flex align-items-center gap-3">
                                <input type="range" class="range-slider" name="theme_border_radius" 
                                       min="0" max="20" value="{{ $settings['theme_border_radius'] ?? '8' }}" 
                                       onchange="updatePreview(); this.nextElementSibling.textContent = this.value + 'px'">
                                <span class="fw-bold">{{ $settings['theme_border_radius'] ?? '8' }}px</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Custom CSS -->
            <div class="customization-panel">
                <div class="section-header">
                    <h5 class="mb-0"><i class="fas fa-code me-2"></i>Custom CSS</h5>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Additional CSS Rules</label>
                    <textarea class="form-control css-editor" name="theme_custom_css" rows="10" 
                              placeholder="/* Add your custom CSS here */&#10;.custom-class {&#10;    color: #333;&#10;    margin: 10px 0;&#10;}">{{ $settings['theme_custom_css'] ?? '' }}</textarea>
                    <small class="text-muted">Add custom CSS to override default styles. Use with caution.</small>
                </div>
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="enable_dark_mode" id="enableDarkMode">
                    <label class="form-check-label" for="enableDarkMode">
                        Enable Dark Mode Support
                    </label>
                </div>
            </div>
        </div>

        <!-- Live Preview -->
        <div class="col-lg-4">
            <div class="theme-preview">
                <h5 class="mb-3"><i class="fas fa-eye me-2"></i>Live Preview</h5>
                
                <div class="live-preview" id="livePreview">
                    <div class="preview-header">
                        <h6 class="mb-0">Website Header</h6>
                    </div>
                    
                    <div class="preview-content">
                        <h1 style="font-family: var(--heading-font);">Heading Example</h1>
                        <p style="font-family: var(--font-family); font-size: var(--font-size);">
                            This is how your website content will look with the selected theme settings. 
                            You can see the font, colors, and styling in real-time.
                        </p>
                        
                        <div class="preview-buttons mb-3">
                            <button class="preview-button">Primary Button</button>
                            <button class="preview-button" style="background: var(--secondary-color);">Secondary</button>
                            <button class="preview-button" style="background: var(--success-color);">Success</button>
                        </div>
                        
                        <div class="preview-card">
                            <h6>Card Example</h6>
                            <p class="mb-0">This is how cards will appear on your website.</p>
                        </div>
                        
                        <div class="preview-card">
                            <h6>Coupon Card</h6>
                            <p class="text-muted">Save 50% on all electronics</p>
                            <button class="preview-button">Get Code</button>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <div class="d-flex justify-content-between text-muted small">
                        <span>Desktop View</span>
                        <span>
                            <i class="fas fa-desktop me-1"></i>
                            <i class="fas fa-tablet-alt me-1 opacity-50"></i>
                            <i class="fas fa-mobile-alt opacity-50"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Action Buttons -->
<div class="action-buttons">
    <button class="btn btn-success floating-btn" onclick="saveTheme()">
        <i class="fas fa-save me-2"></i>Save Changes
    </button>
</div>
@endsection

@push('scripts')
<script>
// Update preview in real-time
function updatePreview() {
    const form = document.getElementById('themeForm');
    const formData = new FormData(form);
    
    // Update CSS variables
    const root = document.documentElement;
    root.style.setProperty('--primary-color', formData.get('theme_primary_color'));
    root.style.setProperty('--secondary-color', formData.get('theme_secondary_color'));
    root.style.setProperty('--success-color', formData.get('theme_success_color'));
    root.style.setProperty('--accent-color', formData.get('theme_accent_color'));
    root.style.setProperty('--font-family', formData.get('theme_font_family'));
    root.style.setProperty('--heading-font', formData.get('theme_heading_font'));
    root.style.setProperty('--font-size', formData.get('theme_font_size') + 'px');
    root.style.setProperty('--border-radius', formData.get('theme_border_radius') + 'px');
    
    // Update preview
    const preview = document.getElementById('livePreview');
    preview.style.cssText = `
        --primary-color: ${formData.get('theme_primary_color')};
        --secondary-color: ${formData.get('theme_secondary_color')};
        --success-color: ${formData.get('theme_success_color')};
        --accent-color: ${formData.get('theme_accent_color')};
        --font-family: ${formData.get('theme_font_family')};
        --heading-font: ${formData.get('theme_heading_font')};
        --font-size: ${formData.get('theme_font_size')}px;
        --border-radius: ${formData.get('theme_border_radius')}px;
    `;
}

// Apply preset theme
function applyPreset(preset) {
    const presets = {
        default: {
            primary: '#007bff',
            secondary: '#6c757d',
            success: '#28a745',
            accent: '#ffc107'
        },
        modern: {
            primary: '#6f42c1',
            secondary: '#e83e8c',
            success: '#20c997',
            accent: '#fd7e14'
        },
        nature: {
            primary: '#28a745',
            secondary: '#20c997',
            success: '#17a2b8',
            accent: '#ffc107'
        },
        sunset: {
            primary: '#fd7e14',
            secondary: '#dc3545',
            success: '#28a745',
            accent: '#ffc107'
        }
    };
    
    if (presets[preset]) {
        document.querySelector('[name="theme_primary_color"]').value = presets[preset].primary;
        document.querySelector('[name="theme_secondary_color"]').value = presets[preset].secondary;
        document.querySelector('[name="theme_success_color"]').value = presets[preset].success;
        document.querySelector('[name="theme_accent_color"]').value = presets[preset].accent;
        
        // Update text inputs
        document.querySelectorAll('.color-picker').forEach(picker => {
            picker.nextElementSibling.value = picker.value;
        });
        
        updatePreview();
        
        // Update active preset
        document.querySelectorAll('.preset-theme').forEach(theme => theme.classList.remove('active'));
        event.target.closest('.preset-theme').classList.add('active');
    }
}

// Save theme
async function saveTheme() {
    const form = document.getElementById('themeForm');
    const formData = new FormData(form);
    
    // Add custom CSS
    formData.append('theme_custom_css', document.querySelector('[name="theme_custom_css"]').value);
    
    try {
        const response = await fetch('/admin/theme', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        });
        
        if (response.ok) {
            Swal.fire({
                icon: 'success',
                title: 'Theme Saved!',
                text: 'Your theme changes have been applied successfully',
                showConfirmButton: false,
                timer: 2000
            });
        } else {
            throw new Error('Failed to save theme');
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Failed to save theme changes'
        });
    }
}

// Reset to default
function resetToDefault() {
    Swal.fire({
        title: 'Reset Theme?',
        text: 'This will reset all theme settings to default values',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, reset it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            applyPreset('default');
            document.querySelector('[name="theme_font_family"]').value = 'Inter, sans-serif';
            document.querySelector('[name="theme_heading_font"]').value = 'Inter, sans-serif';
            document.querySelector('[name="theme_font_size"]').value = 16;
            document.querySelector('[name="theme_border_radius"]').value = 8;
            document.querySelector('[name="theme_custom_css"]').value = '';
            updatePreview();
        }
    });
}

// Preview changes
function previewChanges() {
    window.open('/?preview=1', '_blank');
}

// Color picker sync
document.querySelectorAll('.color-picker').forEach(picker => {
    picker.addEventListener('input', function() {
        this.nextElementSibling.value = this.value;
        updatePreview();
    });
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updatePreview();
});
</script>
@endpush