import Button from 'react-bootstrap/Button';
import Modal from 'react-bootstrap/Modal';
import {currencyFormatter} from "./Formatter";
import {useState} from "react";

const Application = (props) => {
  console.log('rendering application');
  console.log(props.application);

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

  return (
    <>
      <p>
        APPLICATION?!
        {JSON.stringify(props.application)}
      </p>
      {props.application !== null &&
        <Modal className="vote-application" show={true} onHide={props.handleClose}>
          <Modal.Header closeButton>
            <Modal.Title>
              {props.application.title}
            </Modal.Title>
          </Modal.Header>

          <Modal.Body>
            <p>
              {currencyFormatter.format(props.application.amount_requested) + ' '}
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
          </Modal.Body>

          <Modal.Footer>
            <Button variant="success">
              <i className="fa-solid fa-thumbs-up"></i>
              Approve
            </Button>
            <Button variant="danger">
              <i className="fa-solid fa-thumbs-down"></i>
              Reject
            </Button>
          </Modal.Footer>
        </Modal>
      }
    </>
  );
};

export default Application;
