import './App.css';
import {useEffect, useState} from 'react';

function App(props) {
  const [images, setImages] = useState(props.images);

  // Reinitialize image viewer whenever images change
  const initViewer = () => {
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
  };
  useEffect(() => {
    initViewer();
  }, [images]);

  const handleError = (msg) => {
    alert(msg);
  };

  const handleUpload = () => {
    const uploadButton = document.getElementById('upload-button');
    const fileUpload = document.getElementById('file-upload');

    uploadButton.addEventListener('click', () => {
      const file = fileUpload.files[0];
      if (!file) {
        return;
      }

      if (!file.type.contain('image/')) {
        handleError('Only images can be uploaded');
        return;
      }

      const formData = new FormData();
      formData.append('file', file);

      const xhr = new XMLHttpRequest();
      xhr.open('POST', '/images/upload');
      xhr.send(formData);

      xhr.onload = () => {
        console.log(xhr.response);
        if (xhr.status === 200) {
          console.log('success');
        } else {
          handleError('There was a problem uploading that file. Please try again.');
        }
      };
    });
  };

  const handleRemove = async (key) => {
    if (!confirm('Are you sure you want to delete this image?')) {
      return;
    }

    const image = images[key];
    try {
      const response = await fetch('/api/images/remove', {
        method: 'DELETE',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({filename: image.filename}),
      });
      if (!response.ok) {
        handleError(`${response.status} ${response.statusText}`);
      }
    } catch(error) {
      handleError(error.message);
    }
    setImages(images.filter((img) => img.filename !== image.filename));
  };

  const handleMoveDown = (index) => {
    if (index >= images.length - 1) {
      return;
    }

    const newImages = [...images];
    newImages[index] = images[index + 1];
    newImages[index + 1] = images[index];
    setImages(newImages);
  };

  const handleMoveUp = (index) => {
    if (index <= 0) {
      return;
    }

    const newImages = [...images];
    newImages[index] = images[index - 1];
    newImages[index - 1] = images[index];
    setImages(newImages);
  };

  return (
    <div className="image-upload">
      <div className="image-upload__input">
        <input type="file" id="file-upload" className="form-control" aria-label="Select an image to upload" />
        <button type="button" id="upload-button" onClick={handleUpload} className="btn btn-secondary">
          Upload
        </button>
      </div>
      <ul className="image-upload__images list-unstyled image-gallery">
        {images.map((image, key) =>
          <li key={key} className="row">
            <div className="col-1">
              <button type="button" className="image-upload__remove-image btn btn-link" title="Remove" aria-label="Remove"
                      onClick={() => {
                        handleRemove(key)
                      }}>
                <i className="fas fa-times"></i>
              </button>
            </div>
            <div className="col-1">
              {key !== 0 &&
                <>
                  <button type="button" className="image-upload__move-image btn btn-link" title="Move up" aria-label="Move up"
                          onClick={() => {
                            handleMoveUp(key)
                          }}>
                    <i className="fa-solid fa-up-long"></i>
                  </button>
                  <br/>
                </>
              }
              {key !== images.length - 1 &&
                <>
                  <button type="button" className="image-upload__move-image btn btn-link" title="Move down" aria-label="Move down"
                          onClick={() => {
                            handleMoveDown(key)
                          }}>
                    <i className="fa-solid fa-down-long"></i>
                  </button>
                </>
              }
            </div>
            <div className="col-10">
              <img src={'/img/projects/thumb_' + image.filename} data-full={'/img/projects/' + image.filename}
                   title="Click to view full-size" />
              {/*}
              <br/>
              <input aria-label="Caption" type="text" name={'images[' + key + '][caption]'} value={image.caption}
                     className="form-control" placeholder="Enter a caption for this image"
                     aria-placeholder="Enter a caption for this image" />
               */}
              <input type="hidden" name={'images[' + key + '][filename]'} value={image.filename}/>
            </div>
          </li>
        )}
      </ul>
    </div>
  );
}

export default App;
