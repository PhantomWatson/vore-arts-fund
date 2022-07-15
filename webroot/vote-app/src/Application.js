import {currencyFormatter} from "./Formatter";

const Application = (props) => {
  const questionSort = function(a, b) {
    if (a.question.weight < b.question.weight) {
      return -1;
    }
    if (a.question.weight > b.question.weight) {
      return 1;
    }
    return 0;
  }

  let answers = props.application.answers.sort(questionSort);

  return (
    <div className="vote-application">
      <h1>
        {props.application.title}
      </h1>
      <p>
        {currencyFormatter.format(application.amount_requested) + ' '}
        requested by {application.user.name}
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
    </div>
  );
};

export default Application;
