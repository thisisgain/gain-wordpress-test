// Requires all images in current folder
const requireAllImages = require.context(
    './',
    true,
    /\.(png|jpe?g|gif|svg|webp)$/i
);
requireAllImages.keys().forEach(requireAllImages);