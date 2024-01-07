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
  const STATUS_REJECTED = 3;
  const STATUS_REVISION_REQUESTED = 4;
  const noteNeeded = [
    STATUS_REJECTED, // Reject
    STATUS_REVISION_REQUESTED, // Request revision
  ];
  const NOTE_TYPE_NOTE = 'note';
  const NOTE_TYPE_REVISION_REQUESTED = 'revision request';
  const NOTE_TYPE_REJECTION = 'rejection';
  const NOTE_TYPE_MESSAGE = 'message';

  const getAction = (statusId) => statusActions['' + statusId];
  const getButtonLabel = (statusId) => {
    const action = getAction(statusId);
    return ' ' + action.icon + ' ' + action.label;
  };

  // Debugging
  console.log(statusActions);
  console.log('updating automatically?');

  const addNote = () => {
    setSelectedAction('note');
    setSelectedStatusId(null);
  };

  const addMessage = () => {
    setSelectedAction('message');
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
    const action = getAction(statusId);
    if (confirm('Are you sure you want to ' + action.label.toLowerCase() + '?')) {
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
            <i className="fa-solid fa-file-lines"></i> Add private note
          </button>
        </li>
        <li>
          <button className="dropdown-item"
                  data-action="message"
                  type="button"
                  onClick={addMessage}
          >
            <i className="fa-solid fa-message"></i> Send applicant a message
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
                    dangerouslySetInnerHTML={{
                      __html: getButtonLabel(statusId)
                    }}
            >
            </button>
          </li>
        ))}
      </ul>
    </div>
  );

  const getNoteType = () => {
    if (selectedAction === 'note') {
      return NOTE_TYPE_NOTE;
    }
    if (selectedAction === 'message') {
      return NOTE_TYPE_MESSAGE;
    }
    if (selectedStatusId === STATUS_REJECTED) {
      return NOTE_TYPE_REJECTION;
    }
    if (selectedStatusId === STATUS_REVISION_REQUESTED) {
      return NOTE_TYPE_REVISION_REQUESTED;
    }
    return NOTE_TYPE_NOTE;
  };

  const NoteSection = () => {
    return (
      <>
        <div className="form-group textarea required mt-3">
          <label htmlFor="status-change-note" className="form-label">
            {selectedAction === 'note' && "Private note (will not be shown to applicant)"}
            {selectedAction === 'message' && "Message to applicant"}
            {selectedStatusId === STATUS_REJECTED && "Reason for rejection (will be sent to applicant)"}
            {selectedStatusId === STATUS_REVISION_REQUESTED && "What should the applicant add/change? (will be sent to applicant)"}
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
          {selectedAction === 'note' && "Add note"}
          {selectedAction === 'message' && "Send message"}
          {selectedStatusId === STATUS_REJECTED && "Reject application"}
          {selectedStatusId === STATUS_REVISION_REQUESTED && "Request revision"}
        </button>
        <input type="hidden" name="type" value={getNoteType()} />
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
        {(selectedAction === 'note' || selectedAction === 'message') &&
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
