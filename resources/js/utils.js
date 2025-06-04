// Helper function to get DaisyUI colors from CSS variables
export function getColors() {
    const style = getComputedStyle(document.documentElement);
    return {
        primary: style.getPropertyValue('--color-primary').trim() || '#570df8',
        secondary: style.getPropertyValue('--color-secondary').trim() || '#f000b8',
        accent: style.getPropertyValue('--color-accent').trim() || '#37cdbe',
        neutral: style.getPropertyValue('--color-neutral').trim() || '#3d4451',
        base100: style.getPropertyValue('--color-base-100').trim() || '#ffffff',
        base200: style.getPropertyValue('--color-base-200').trim() || '#f2f2f2',
        base300: style.getPropertyValue('--color-base-300').trim() || '#e5e6e6',
        baseContent: style.getPropertyValue('--color-base-content').trim() || '#1f2937',
        info: style.getPropertyValue('--color-info').trim() || '#3abff8',
        success: style.getPropertyValue('--color-success').trim() || '#36d399',
        warning: style.getPropertyValue('--color-warning').trim() || '#fbbd23',
        error: style.getPropertyValue('--color-error').trim() || '#f87272',
    };
}
