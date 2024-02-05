if (document.querySelector('.image-gallery')) {
  new Viewer(
    document.querySelector('.image-gallery'),
    {
      url: 'data-full',
      toolbar: {
        zoomIn: true,
        zoomOut: true,
        oneToOne: false,
        reset: true,
        prev: true,
        play: false,
        next: true,
        rotateLeft: false,
        rotateRight: false,
        flipHorizontal: false,
        flipVertical: false,
      },
    }
  );
}
