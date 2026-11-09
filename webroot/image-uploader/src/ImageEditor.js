import 'cropperjs/dist/cropper.css';
import './ImageEditor.css';
import Cropper from 'cropperjs';
import {useEffect, useId, useLayoutEffect, useRef, useState} from 'react';

const KEYBOARD_STEP = 10;

// Matches ImageProcessor::MAX_WIDTH_FULL and MAX_HEIGHT_FULL, since anything larger would be scaled down by the server
const MAX_OUTPUT_DIMENSION = 2000;

const OUTPUT_EXTENSIONS = {
  'image/jpeg': 'jpg',
  'image/png': 'png',
  'image/webp': 'webp',
};

const OUTPUT_QUALITY = 0.95;

const KEY_DIRECTIONS = {
  ArrowDown: [0, 1],
  ArrowLeft: [-1, 0],
  ArrowRight: [1, 0],
  ArrowUp: [0, -1],
};

/**
 * Renders the edited image into a new file
 *
 * The server chooses an image decoder based on the file extension, so the extension must come from the format that the
 * browser actually produced, which can differ from the requested format (e.g. Safari can't encode WEBP).
 */
const exportFile = (cropper, file) => new Promise((resolve, reject) => {
  const canvas = cropper.getCroppedCanvas({
    imageSmoothingQuality: 'high',
    maxHeight: MAX_OUTPUT_DIMENSION,
    maxWidth: MAX_OUTPUT_DIMENSION,
  });
  if (!canvas) {
    reject(new Error('There was a problem editing that image. Please try again.'));
    return;
  }

  const type = OUTPUT_EXTENSIONS[file.type] ? file.type : 'image/png';
  canvas.toBlob((blob) => {
    if (!blob) {
      reject(new Error('There was a problem editing that image. Please try again.'));
      return;
    }
    const name = `${file.name.replace(/\.[^.]*$/, '')}.${OUTPUT_EXTENSIONS[blob.type]}`;
    resolve(new File([blob], name, {type: blob.type}));
  }, type, OUTPUT_QUALITY);
});

/**
 * Modal dialog for cropping and rotating an image
 *
 * @param {string} applyLabel Text of the button that accepts the edits
 * @param {File} file The image to edit
 * @param {function} onApply Async function that receives the edited File. The dialog stays open until the parent
 *   stops rendering it, so a failed upload doesn't lose the user's edits.
 * @param {function} onCancel Called when the user dismisses the dialog
 * @param {function} onError Called with a message when something goes wrong
 */
function ImageEditor({applyLabel, file, onApply, onCancel, onError}) {
  const [imageUrl, setImageUrl] = useState(null);
  const [isReady, setIsReady] = useState(false);
  const [isSaving, setIsSaving] = useState(false);
  const cropperRef = useRef(null);
  const dialogRef = useRef(null);
  const imageRef = useRef(null);
  const id = useId();
  const headingId = `${id}-heading`;
  const instructionsId = `${id}-instructions`;

  useEffect(() => {
    const url = URL.createObjectURL(file);
    setImageUrl(url);
    return () => {
      URL.revokeObjectURL(url);
    };
  }, [file]);

  // A layout effect, because closing the dialog returns focus to the file input only if it's still in the DOM
  useLayoutEffect(() => {
    const dialog = dialogRef.current;
    dialog.showModal();
    return () => {
      dialog.close();
    };
  }, []);

  useEffect(() => () => {
    if (cropperRef.current) {
      cropperRef.current.destroy();
      cropperRef.current = null;
    }
  }, []);

  const handleImageLoad = () => {
    if (cropperRef.current) {
      return;
    }

    cropperRef.current = new Cropper(imageRef.current, {
      autoCropArea: 1,
      movable: false,
      ready: () => {
        setIsReady(true);
      },
      viewMode: 1,
      zoomable: false,
    });
  };

  const handleImageError = () => {
    onError('That file could not be read as an image.');
    onCancel();
  };

  /**
   * Rotates the image by a quarter turn, keeping the crop box on the same part of the image and the whole image visible
   */
  const handleRotate = (degrees) => {
    const cropper = cropperRef.current;
    const oldCanvas = cropper.getCanvasData();
    const oldCropBox = cropper.getCropBoxData();
    const relativeLeft = (oldCropBox.left - oldCanvas.left) / oldCanvas.width;
    const relativeTop = (oldCropBox.top - oldCanvas.top) / oldCanvas.height;
    const relativeWidth = oldCropBox.width / oldCanvas.width;
    const relativeHeight = oldCropBox.height / oldCanvas.height;

    cropper.rotate(degrees);

    // Cropper won't let the image be smaller than the crop box or moved off of it, so collapse the box into the center
    const container = cropper.getContainerData();
    cropper.setCropBoxData({height: 0, left: container.width / 2, top: container.height / 2, width: 0});

    const {naturalHeight, naturalWidth} = cropper.getCanvasData();
    const width = Math.min(container.width, container.height * naturalWidth / naturalHeight);
    const height = width * naturalHeight / naturalWidth;
    const left = (container.width - width) / 2;
    const top = (container.height - height) / 2;
    cropper.setCanvasData({left, top, width});

    cropper.setCropBoxData({
      height: relativeWidth * height,
      left: left + width * (degrees > 0 ? 1 - relativeTop - relativeHeight : relativeTop),
      top: top + height * (degrees > 0 ? relativeLeft : 1 - relativeLeft - relativeWidth),
      width: relativeHeight * width,
    });
  };

  const handleStageKeyDown = (event) => {
    const direction = KEY_DIRECTIONS[event.key];
    const cropper = cropperRef.current;
    if (!direction || !cropper) {
      return;
    }

    event.preventDefault();
    const [deltaX, deltaY] = direction.map((value) => value * KEYBOARD_STEP);
    const cropBox = cropper.getCropBoxData();
    cropper.setCropBoxData(event.shiftKey
      ? {height: cropBox.height + deltaY, width: cropBox.width + deltaX}
      : {left: cropBox.left + deltaX, top: cropBox.top + deltaY});
  };

  const handleApply = async () => {
    setIsSaving(true);
    try {
      await onApply(await exportFile(cropperRef.current, file));
    } catch (error) {
      onError(error.message);
    }
    setIsSaving(false);
  };

  const handleDialogCancel = (event) => {
    event.preventDefault();
    if (!isSaving) {
      onCancel();
    }
  };

  const isDisabled = !isReady || isSaving;

  return (
    <dialog aria-labelledby={headingId} className="image-editor" onCancel={handleDialogCancel} ref={dialogRef}>
      <h2 className="image-editor__heading" id={headingId}>Crop and rotate image</h2>
      <p className="image-editor__instructions" id={instructionsId}>
        Drag the edges of the box to crop. With the keyboard, focus the image, then use the arrow keys to move the box,
        or hold Shift and use the arrow keys to resize it.
      </p>
      <div
        aria-describedby={instructionsId}
        aria-label="Crop area"
        className="image-editor__stage"
        onKeyDown={handleStageKeyDown}
        role="group"
        tabIndex={0}
      >
        {imageUrl &&
          <img alt="" onError={handleImageError} onLoad={handleImageLoad} ref={imageRef} src={imageUrl} />
        }
      </div>
      <div className="image-editor__toolbar">
        <div className="image-editor__tools">
          <button className="btn btn-secondary" disabled={isDisabled} onClick={() => handleRotate(-90)} type="button">
            <i aria-hidden="true" className="fa-solid fa-rotate-left"></i> Rotate left
          </button>
          <button className="btn btn-secondary" disabled={isDisabled} onClick={() => handleRotate(90)} type="button">
            <i aria-hidden="true" className="fa-solid fa-rotate-right"></i> Rotate right
          </button>
          <button className="btn btn-secondary" disabled={isDisabled} onClick={() => cropperRef.current.reset()} type="button">
            Reset
          </button>
        </div>
        <div className="image-editor__tools">
          <button className="btn btn-link" disabled={isSaving} onClick={onCancel} type="button">
            Cancel
          </button>
          <button className="btn btn-primary" disabled={isDisabled} onClick={handleApply} type="button">
            {applyLabel}
            {isSaving && <i className="loading-indicator fa-solid fa-spinner fa-spin-pulse" title="Loading"></i>}
          </button>
        </div>
      </div>
    </dialog>
  );
}

export default ImageEditor;
