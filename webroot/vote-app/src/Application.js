import Button from 'react-bootstrap/Button';
import Modal from 'react-bootstrap/Modal';
import {currencyFormatter} from "./Formatter";
import {useEffect} from "react";
import Viewer from "viewerjs";

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
  const voteYes = () => {
    props.handleVote(props.application.id, true);
    props.handleClose();
  }
  const voteNo = () => {
    props.handleVote(props.application.id, false);
    props.handleClose();
  }

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
              <strong>Category:</strong> {props.application.category.name}
            </p>
            <p>
              {currencyFormatter.format(props.application.amount_requested) + ' '}
              is being requested by {props.application.user.name}
            </p>
            <section>
              <h2>
                Project description
              </h2>
              <p>
                {props.application.description}
              </p>
            </section>
            {answers.map((answer, index) => {
              return (
                <section className="vote-application-qa" key={index}>
                  <h2 className="vote-application-q">
                    {answer.question.question}
                  </h2>
                  <p className="vote-application-a">
                    {answer.answer}
                  </p>
                </section>
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
            <Button variant="success" onClick={voteYes}>
              <i className="fa-solid fa-thumbs-up"></i>
              &nbsp;
              Approve
            </Button>
            <Button variant="danger" onClick={voteNo}>
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
