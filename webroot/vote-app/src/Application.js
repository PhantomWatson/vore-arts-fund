import Button from 'react-bootstrap/Button';
import Modal from 'react-bootstrap/Modal';
import {currencyFormatter} from "./Formatter";
import {useEffect} from "react";

const Application = (props) => {
  const devMode = !process.env.NODE_ENV || process.env.NODE_ENV === 'development';
  const imgBase = devMode ? 'http://vore.test:9000' : '';

  const questionSort = function(a, b) {
    if (a.question.weight < b.question.weight) {
      return -1;
    }
    if (a.question.weight > b.question.weight) {
      return 1;
    }
    return 0;
  }

  let answers = props.application ? props.application.answers.sort(questionSort) : [];

  useEffect(() => {
    const gallery = document.querySelector('.image-gallery');
    if (!gallery) {
      return;
    }
    new Viewer(
      gallery,
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
  });

  return (
    <>
      {props.application !== null &&
        <Modal className="vote-application" show={true} onHide={props.handleClose}>
          <Modal.Header closeButton>
            <Modal.Title>
              {props.application.title}
            </Modal.Title>
          </Modal.Header>

          <Modal.Body>
            <p>
              Description: {currencyFormatter.format(props.application.amount_requested) + ' '}
              requested by {props.application.user.name}
            </p>
            <p>
              Category: {props.application.category.name}
            </p>
            <p>
              {props.application.description}
            </p>
            {answers.map((answer, index) => {
              return (
                <div className="vote-application-qa" key={index}>
                  <p className="vote-application-q">
                    {answer.question.question}
                  </p>
                  <p className="vote-application-a">
                    {answer.answer}
                  </p>
                </div>
              );
            })}
            <div className="image-gallery">
              {props.application.images.map((image, index) => {
                return (
                  <img src={`${imgBase}/img/applications/thumb_${image.filename}`} key={index}
                       alt="Image submitted with application" className="img-thumbnail"
                       title="Click to open full-size image"
                       data-full={`${imgBase}/img/applications/${image.filename}`} />
                );
              })}
            </div>
          </Modal.Body>

          <Modal.Footer>
            <Button variant="success">
              <i className="fa-solid fa-thumbs-up"></i>
              &nbsp;
              Approve
            </Button>
            <Button variant="danger">
              <i className="fa-solid fa-thumbs-down"></i>
              &nbsp;
              Reject
            </Button>
          </Modal.Footer>
        </Modal>
      }
    </>
  );
};

export default Application;
