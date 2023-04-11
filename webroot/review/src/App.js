import * as React from 'react';
import {useState} from 'react';

const App = () => {
  const statusActions = window.statusActions;
  const validStatusIds = window.validStatusIds;
  const [selectedAction, setSelectedAction] = useState(null);
  const [selectedStatusId, setSelectedStatusId] = useState(null);
  const actions = {
    draft: 0,
    submit: 1,
    accept: 2,
    reject: 3,
    requestRevision: 4,
    award: 6,
    declineToAward: 7,
    withdraw: 8,
  };
  const noteNeeded = [
    3,
    4,
  ];

  const getActionName = (statusId) => statusActions['' + statusId];

  const addNote = () => {
    setSelectedAction('note');
    setSelectedStatusId(null);
  };

  const submitStatusChange = (statusId) => {
    const formId = 'submit-status-change';
    document.body.innerHTML += '<form id="' + formId + '" method="post"><input type="hidden" name="status_id" value="' + statusId + '"></form>';
    document.getElementById(formId).submit();
  };

  const statusChangeRequiresNote = (statusId) => noteNeeded.indexOf(statusId) === -1;

  const changeStatus = (statusId) => {
    setSelectedAction('change status');
    setSelectedStatusId(statusId);

    // Submit
    if (statusChangeRequiresNote(statusId)) {
      const action = statusActions['' + statusId].toLowerCase();
      if (confirm('Are you sure you want to ' + action + '?')) {
        submitStatusChange(statusId);

        return;
      }
    }
  };

  const Menu = () => (
    <div className="dropdown">
      <button className="btn btn-primary dropdown-toggle"
              type="button"
              data-bs-toggle="dropdown"
              aria-expanded="false"
      >
        Actions
      </button>
      <ul className="dropdown-menu" id="review-actions">
        <li>
          <button className="dropdown-item"
                  data-action="note"
                  type="button"
                  onClick={addNote}
          >
            Add note
          </button>
        </li>
        {validStatusIds.map(statusId => (
          <li key={statusId}>
            <button className="dropdown-item"
                    data-status-id={statusId}
                    data-action="changeStatus"
                    type="button"
                    onClick={() => {
                      changeStatus(statusId)
                    }}
            >
              {getActionName(statusId)}
            </button>
          </li>
        ))}
      </ul>
    </div>
  );

  const NoteSection = () => {
    return (
      <>
        <div className="form-group textarea required mt-3">
          <label htmlFor="status-change-note" className="form-label">
            {selectedStatusId === null && "Note"}
            {selectedStatusId === 3 && "Reason for rejection"}
            {selectedStatusId === 4 && "What should the applicant add/change?"}
          </label>
          <textarea className="form-control"
                    id="status-change-note"
                    required="required"
                    aria-required="true"
                    rows="5"
                    name="body">
          </textarea>
        </div>
        <button type="submit" className="btn btn-primary">
          {selectedStatusId === null && "Add note"}
          {selectedStatusId === 3 && "Reject application"}
          {selectedStatusId === 4 && "Request revision"}
        </button>
      </>
    );
  };

  const ChangeStatusSection = () => {
    return (
      <div>
        {statusChangeRequiresNote(selectedStatusId) &&
          <NoteSection />
        }
      </div>
    );
  };

  return (
    <>
      <Menu />
      <form method="post">
        {selectedAction === 'note' &&
          <NoteSection />
        }
        {selectedAction === 'change status' &&
          <ChangeStatusSection statusId={selectedStatusId} />
        }
      </form>
    </>
  );
};

export default App;

/*
const selectedAction = null;

function selectAction(action, statusId) {

}

const actionLinks = document.querySelectorAll('#review-actions a');
actionLinks.forEach(function (actionLink) {
  actionLink.addEventListener('click', () => {
    selectAction(actionLink.dataset.action, actionLink.dataset.statusId);
  });
});
const revReqNoteField = document.getElementById('revision-requested-note');
const revReqNoteContainer = revReqNoteField.parentElement;
const statusChangeField = document.getElementById('change-status');
const toggleNoteField = () => {
  if (parseInt(statusChangeField.value) === actions.requestRevision) {
    statusChangeField.required = true;
    revReqNoteContainer.style.display = 'block';
  } else {
    statusChangeField.required = false;
    revReqNoteContainer.style.display = 'none';
  }
}
document.getElementById('change-status').addEventListener('change', () => {toggleNoteField();});
toggleNoteField();
*/
