/**
 * Image helper utilities for product images and placeholders
 */

/**
 * Generate a data URL for a placeholder image
 * @param {number} width - Image width
 * @param {number} height - Image height 
 * @param {string} text - Text to display in placeholder
 * @param {string} bgColor - Background color (hex without #)
 * @param {string} textColor - Text color (hex without #)
 * @returns {string} Data URL for SVG placeholder
 */
export function generatePlaceholderDataUrl(width = 400, height = 400, text = 'Produkt', bgColor = '4f46e5', textColor = 'ffffff') {
  const svg = `
    <svg width="${width}" height="${height}" xmlns="http://www.w3.org/2000/svg">
      <rect width="100%" height="100%" fill="#${bgColor}"/>
      <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#${textColor}" font-family="Arial, sans-serif" font-size="${Math.min(width, height) / 8}px">
        ${text}
      </text>
    </svg>
  `.trim();
  
  return `data:image/svg+xml;base64,${btoa(svg)}`;
}

/**
 * Get product image URL with fallback to local placeholder
 * @param {string|null} imageUrl - Original image URL
 * @param {string} productName - Product name for placeholder text
 * @param {number} width - Placeholder width
 * @param {number} height - Placeholder height
 * @returns {string} Image URL or placeholder data URL
 */
export function getProductImageUrl(imageUrl, productName = 'Produkt', width = 400, height = 400) {
  // If no image URL provided, use placeholder
  if (!imageUrl) {
    return generatePlaceholderDataUrl(width, height, productName);
  }
  
  // Skip via.placeholder.com URLs
  if (imageUrl.includes('via.placeholder.com')) {
    return generatePlaceholderDataUrl(width, height, productName);
  }
  
  // If it's already a full URL (http/https), return as is
  if (imageUrl.startsWith('http://') || imageUrl.startsWith('https://')) {
    return imageUrl;
  }
  
  // Remove any leading slashes and normalize path separators
  const cleanPath = imageUrl.replace(/^\/+/, '').replace(/\\/g, '/');
  
  // Get filename without path
  const filename = cleanPath.split('/').pop();
  
  // First, check if it's a pre-seeded product image
  if (availableProductImages.includes(filename)) {
    return `/img/${filename}`;
  }
  
  // For all other cases, return the storage path
  return `/storage/products/${filename}`;
}

/**
 * Handle image error and set fallback
 * @param {Event} event - Image error event
 * @param {string} productName - Product name for placeholder
 * @param {number} width - Placeholder width  
 * @param {number} height - Placeholder height
 */
export function handleImageError(event, productName = 'Produkt', width = 400, height = 400) {
  const img = event.target;
  const currentSrc = img.src;
  
  // Avoid infinite loops
  if (currentSrc.startsWith('data:image/svg+xml') || img.dataset.triedFallback === 'true') {
    return;
  }
  
  // Mark that we've tried fallback for this image
  img.dataset.triedFallback = 'true';
  
  // Get clean filename without path or query params
  let filename = currentSrc.split(/[\/\\]/).pop().split('?')[0];
  
  // If it's in availableProductImages, use /img/
  if (availableProductImages.includes(filename)) {
    img.src = `/img/${filename}`;
    return;
  }
  
  // If not found in availableProductImages, use placeholder
  img.src = generatePlaceholderDataUrl(width, height, productName);
}

/**
 * Available product images in public/img directory
 */
export const availableProductImages = [
  'harrows-lotki-harrows-fire-inferno.webp',
  'harrows-lotki-harrows-noble.webp', 
  'harrows-lotki-harrows-supergrip-ultra.webp',
  'target-lotki-target-975-ultra-marine-02-swiss-poin.webp',
  'target-lotki-target-rob-cross-g1-swiss-point.webp',
  'target-lotki-target-sebastian-bialecki-g1-swiss-po.webp',
  'unicorn-lotki-unicorn-gary-anderson-wc-phase-3.webp',
  'unicorn-lotki-unicorn-premier-james-wade.webp',
  'unicorn-lotki-unicorn-ross-smith-two-tone.jpg',
  'winmau-lotki-winmau-blackout-1.jpg',
  'winmau-lotki-winmau-michael-van-gerwen-exact.webp',
  'winmau-lotki-winmau-sniper-v3.webp',
  'target-piorka-target-crux-3-sets-pro-ultra-no6.jpg',
  'target-piorka-target-cult-pro-ultra-no6.jpg',
  'target-piorka-target-nathan-aspinall-pro-ultra-no6.webp',
  'unicorn-piorka-unicorn-ultrafly-gary-anderson-phas.webp',
  'unicorn-piorka-unicorn-ultrafly-james-wade.webp',
  'winmau-piorka-winmau-michael-van-gerwen-standard-b.webp',
  'winmau-piorka-winmau-prims-alpha-joe-cullen.webp',
  'target-oswietlenie-tarczy-target-corona-vision.webp',
  'winmau-oswietlenie-tarczy-winmau-plasma.webp',
  'target-tarcza-target-tor-profesjonalne.webp',
  'unicorn-tarcza-unicorn-eclipse-ultra-profesjonalne.webp',
  'winmau-tarcza-winmau-blade-6-profesjonalne.webp',
  'winmau-shafty-winmau-prism-force-red.webp',
  'winmau-shafty-winmau-prism-shaft-blue.webp',
  'target-target-pro-tour-dartboard-surround-black.webp',
  'winmau-winmau-surround-pro-line-blade-6.webp'
];

/**
 * Get a random product image from available images
 * @returns {string} Path to random product image
 */
export function getRandomProductImage() {
  const randomIndex = Math.floor(Math.random() * availableProductImages.length);
  return `/img/${availableProductImages[randomIndex]}`;
} 