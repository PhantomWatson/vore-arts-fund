import {useEffect, useState} from 'react'
import { Formik, Form, Field } from 'formik';

const testConfig = {
  action: 'add',
  cycles: [{id: 1, name: 'Cycle name', projects: [{id: 3, title: 'Project name'}]}],
  transactionTypes: [{id: 1, name: 'Donation'}, {id: 2, name: 'Bribe'}],
  endpointUrl: '/api/transactions',
};
//window.transactionForm = testConfig;

/*
 * Config values that could be set in window.transactionForm:
 *  - action: add|edit
 *  - cycles: [{id: 1, name: 'Cycle name', projects: [{id: 3, title: 'Project name'}]}],
 *  - transactionTypes: [{id: ..., name:...}]
 *  - endpointURL: ...
 */

const getConfig = () => {
  const config = window.transactionForm;
  console.log(config);
  const action = config.action ?? false;
  let errorLoadingConfig = '';
  if (action !== 'add' && action !== 'edit') {
    errorLoadingConfig += 'Invalid value for window.transactionForm.action. ';
  }
  const transactionTypes = config.transactionTypes ?? false;
  if (!transactionTypes) {
    errorLoadingConfig += 'window.transactionForm.transactionTypes not set. ';
  }
  const endpointUrl = config.endpointUrl ?? false;
  if (!endpointUrl) {
    errorLoadingConfig += 'window.transactionForm.endpointUrl not set. ';
  }
  const cycles = config.cycles ?? [];
  const transaction = config.transaction ?? {};

  return {action, transactionTypes, endpointUrl, cycles, transaction, errorLoadingConfig};
}

// Config
const {action, transactionTypes, endpointUrl, projects, cycles, transaction, errorLoadingConfig} = getConfig();

const App = () => {
  // State
  const [errorMsg, setErrorMsg] = useState('');
  const [showForm, setShowForm] = useState(true);
  const [transactionType, setTransactionType] = useState('');

  useEffect(() => {
    if (errorLoadingConfig) {
      setErrorMsg(errorLoadingConfig);
      setShowForm(false);
    }
    if (typeof preventMultipleSubmit !== 'undefined') {
      preventMultipleSubmit('#transaction-form');
    }
  }, []);

  // Handlers
  const onSubmit = () => {};
  const onDelete = () => {
    if (!confirm(`Are you sure you want to delete this transaction?`)) {
      return;
    }
  };

  const isSelectedProject = (projectId) => {
    const selectedProjectId = transaction.project_id ?? false;
    if (!selectedProjectId) {
      return false;
    }
    return +selectedProjectId === +projectId;
  };

  return (
    <>
      {errorMsg && (
        <div className="alert alert-error" onClick="this.classList.add('hidden');">
          <div className="container">
            {errorMsg}
          </div>
        </div>
      )}
      {action === 'edit' && (
        <form method="DELETE" onSubmit={onDelete}>
          <button type="submit" className="btn btn-danger">
            Delete
          </button>
        </form>
      )}
      {showForm && (
        <Formik initialValues={transaction} onSubmit={onSubmit}>
          {({}) => (
            <Form method="POST" id="transaction-form">
              <fieldset id="form__transaction">
                <div className="form-group">
                  <label htmlFor="date">Date</label>
                  <Field className="form-control"
                         type="datetime-local"
                         name="date"
                         required="required"
                         step="60" />
                </div>

                <div className="form-group number required">
                  <label htmlFor="type">Type</label>
                  <Field component="select" className="form-control" name="type" id="type" required="required">
                    <option value=""></option>
                    {Object.entries(transactionTypes).map(([id, name]) => (
                      <option key={id} value={name}>
                        {name}
                      </option>
                    ))}
                  </Field>
                </div>

                <label htmlFor="name">
                  Name
                </label>
                <div className="row">
                  <div className="col-sm-6">
                    <div className="form-group text">
                      <Field className="form-control" type="text" name="name" id="name" maxLength="100" />
                    </div>
                  </div>
                  <div className="col-sm-6">
                    <p>The person or business that money was paid to or received from</p>
                  </div>
                </div>

                <div className="required">
                  <label htmlFor="amount-gross">
                    Amount (gross)
                  </label>
                </div>

                <div className="row">
                  <div className="col-sm-6">
                    <div className="form-group number required">
                      <Field className="form-control" type="number"
                             name="amount_gross" min="0" step="0.01"
                             required="required"
                             data-validity-message="This field cannot be left empty"
                             onInvalid={() => {this.setCustomValidity(''); if (!this.value) this.setCustomValidity(this.dataset.validityMessage)}}
                             onInput={() => {this.setCustomValidity('')}}
                             id="amount-gross" aria-required="true" />
                    </div>
                  </div>
                  <div className="col-sm-6">
                    <p>The amount paid or received</p>
                  </div>
                </div>

                <div className="required">
                  <label htmlFor="amount-net">
                    Amount (net)
                  </label>
                </div>
                <div className="row">
                  <div className="col-sm-6">
                    <div className="form-group number required">
                      <Field className="form-control" type="number"
                             name="amount_net" min="0" step="0.01"
                             required="required"
                             data-validity-message="This field cannot be left empty"
                             onInvalid={() => {this.setCustomValidity(''); if (!this.value) this.setCustomValidity(this.dataset.validityMessage)}}
                             onInput={() => {this.setCustomValidity('')}} id="amount-net"
                             aria-required="true" />
                    </div>
                  </div>
                  <div className="col-sm-6">
                    <p>The gross amount, minus any processing fees</p>
                  </div>
                </div>

                <label htmlFor="project-id">
                  Project
                </label>
                <div className="row">
                  <div className="col-sm-6">
                    <div className="form-group select">
                      <Field component="select" className="form-control" name="project_id" id="project-id">
                        <option value=""></option>
                        {Object.entries(cycles).map(([id, cycle]) => (
                          cycle.hasOwnProperty('projects')
                            ? (
                              <optgroup key={'cycle-' + id} label={cycle.name}>
                                {cycle.projects.map((project) => (
                                    <option key={'project-' + project.id} value={project.id} selected={isSelectedProject(project.id)}>
                                      {project.title}
                                    </option>
                                  ))
                                }
                              </optgroup>
                            )
                            : ''
                        ))}
                      </Field>
                    </div>
                  </div>
                  <div className="col-sm-6">
                    <p>(Optional) The project that this transaction is associated with</p>
                  </div>
                </div>

                <label htmlFor="meta">
                  Metadata
                </label>
                <div className="row">
                  <div className="col-sm-6">
                    <div className="form-group textarea">
                      <Field component="textarea" className="form-control" name="meta" id="meta" rows="5"></Field>
                    </div>
                  </div>
                  <div className="col-sm-6">
                    <p>
                      This is for any other information about this transaction that's important to remember.
                      For any checks written, the check number should be recorded here.
                    </p>
                  </div>
                </div>
              </fieldset>
              <button type="submit" className="btn btn-primary">
                {action === 'add' ? 'Add' : 'Update'}
              </button>
            </Form>
          )}
        </Formik>
      )}
    </>
  )
};

export default App;
