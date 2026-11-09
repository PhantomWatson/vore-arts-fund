import {useEffect, useState} from 'react';
import ImageEditor from './ImageEditor';

/**
 * Adds the image editor to a file input in a server-rendered form
 *
 * The edited image replaces the input's selected file, so the form submits it normally, and the input still works as a
 * plain file input if this script doesn't load.
 *
 * @param {HTMLInputElement} input
 */
function EditableFileInput({input}) {
  const [file, setFile] = useState(null);

  useEffect(() => {
    const handleChange = () => {
      const selectedFile = input.files[0];
      if (!selectedFile) {
        return;
      }

      if (!selectedFile.type.includes('image/')) {
        alert('Only images can be uploaded');
        input.value = '';
        return;
      }

      setFile(selectedFile);
    };

    input.addEventListener('change', handleChange);
    return () => {
      input.removeEventListener('change', handleChange);
    };
  }, [input]);

  if (!file) {
    return null;
  }

  const handleApply = (editedFile) => {
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(editedFile);
    input.files = dataTransfer.files;
    setFile(null);
  };

  const handleCancel = () => {
    input.value = '';
    setFile(null);
  };

  return (
    <ImageEditor
      applyLabel="Use this image"
      file={file}
      key={file.name + file.lastModified}
      onApply={handleApply}
      onCancel={handleCancel}
      onError={alert}
    />
  );
}

export default EditableFileInput;
