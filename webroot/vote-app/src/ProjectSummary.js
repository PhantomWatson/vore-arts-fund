import {currencyFormatter} from "./Formatter";

const ProjectSummary = (props) => {
  return (
    <>
      <p className="project-title">
        {props.project.title}
      </p>
      <p className="project-summary">
        {props.project.accept_partial_payout && 'Up to '}
        {currencyFormatter.format(props.project.amount_requested) + ' '}
        requested by {props.project.user.name}
      </p>
    </>
  );
};

export default ProjectSummary;
