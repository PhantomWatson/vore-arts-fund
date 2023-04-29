import * as React from 'react';
import {useState} from 'react';

/**
 * Review interface
 *
 * draft: 0,
 * submit: 1,
 * accept: 2,
 * reject: 3,
 * requestRevision: 4,
 * award: 6,
 * declineToAward: 7,
 * withdraw: 8,
 *
 * @returns {JSX.Element}
 * @constructor
 */
const App = () => {
  const statusActions = window.statusActions;
  const validStatusIds = window.validStatusIds;
  const [selectedAction, setSelectedAction] = useState(null);
  const [selectedStatusId, setSelectedStatusId] = useState(null);
  const noteNeeded = [
    3, // Reject
    4, // Request revision
  ];

  const getActionName = (statusId) => statusActions['' + statusId];

  const addNote = () => {
    setSelectedAction('note');
    setSelectedStatusId(null);
  };

  const reset = () => {
    setSelectedAction(null);
    setSelectedStatusId(null);
  }

  const submitStatusChange = (statusId) => {
    const formId = 'submit-status-change';
    document.body.innerHTML += '<form id="' + formId + '" method="post"><input type="hidden" name="status_id" value="' + statusId + '"></form>';
    document.getElementById(formId).submit();
  };

  const statusChangeRequiresNote = (statusId) => noteNeeded.indexOf(statusId) !== -1;

  const changeStatus = (statusId) => {
    setSelectedAction('change status');
    setSelectedStatusId(statusId);

    // Show form
    if (statusChangeRequiresNote(statusId)) {
      return;
    }

    // Submit
    const action = statusActions['' + statusId].toLowerCase();
    if (confirm('Are you sure you want to ' + action + '?')) {
      submitStatusChange(statusId);

      return;
    }

    reset();
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
        <input type="hidden" name="status_id" value={selectedStatusId} />
      </form>
    </>
  );
};

export default App;
