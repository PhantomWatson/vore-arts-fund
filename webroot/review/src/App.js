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
  };

  const changeStatus = (statusId) => {
    setSelectedAction('change status');
    setSelectedStatusId(statusId);
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
    return (<span>Note</span>);
  };

  const ChangeStatusSection = () => {
    const noteIsNeeded = noteNeeded.indexOf(selectedStatusId) !== -1;

    return (
      <div>
        Change Status to {selectedStatusId}
        {noteIsNeeded &&
          <span>
            Note is needed
          </span>
        }
      </div>
    );
  };

  return (
    <>
      <Menu />
      {selectedAction === 'note' &&
        <NoteSection />
      }
      {selectedAction === 'change status' &&
        <ChangeStatusSection statusId={selectedStatusId} />
      }
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
