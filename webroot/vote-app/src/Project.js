import Button from 'react-bootstrap/Button';
import Modal from 'react-bootstrap/Modal';
import {currencyFormatter} from "./Formatter";
import {useEffect} from "react";
import Viewer from "viewerjs";

const Project = (props) => {
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
  let answers = props.project ? props.project.answers.sort(questionSort) : [];
  const voteYes = () => {
    props.handleVote(props.project.id, true);
    props.handleClose();
  }
  const voteNo = () => {
    props.handleVote(props.project.id, false);
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
        transition: false,
      }
    );
  });

  return (
    <>
      {props.project !== null &&
        <Modal className="vote-project" show={true} onHide={props.handleClose}>
          <Modal.Header closeButton>
            <Modal.Title>
              {props.project.title}
            </Modal.Title>
          </Modal.Header>

          <Modal.Body>
            <p>
              <strong>Category:</strong> {props.project.category.name}
            </p>
            <p>
              {props.project.accept_partial_payout && 'Up to '}
              {currencyFormatter.format(props.project.amount_requested) + ' '}
              is being requested by {props.project.user.name}
            </p>
            <section>
              <h2>
                Project description
              </h2>
              <p>
                {props.project.description}
              </p>
            </section>
            {answers.map((answer, index) => {
              return (
                <section className="vote-project-qa" key={index}>
                  <h2 className="vote-project-q">
                    {answer.question.question}
                  </h2>
                  <p className="vote-project-a">
                    {answer.answer}
                  </p>
                </section>
              );
            })}
            <div className="image-gallery">
              {props.project.images.map((image, index) => {
                return (
                  <img src={`${imgBase}/img/projects/thumb_${image.filename}`} key={index}
                       alt="Image submitted with application" className="img-thumbnail"
                       title="Click to open full-size image"
                       data-full={`${imgBase}/img/projects/${image.filename}`} />
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

export default Project;
