import {useEffect, useState} from 'react'
import { Formik, Form, Field, useFormikContext } from 'formik';
import {TYPE_LOAN, TYPE_DONATION, TYPE_CANCELED_CHECK, TYPE_LOAN_REPAYMENT} from './transactionTypes.js';
import {guidanceName, guidanceMetadata, guidanceProject, guidanceAmountGross} from './guidance.js';
import {showAmountNet, showProjectSelector} from './conditionalRendering.js';
import {getConfig} from './config.js';
import API from './api.js';

// Config
const {action, transactionTypes, endpointUrl, cycles, transaction, errorLoadingConfig} = getConfig();

async function setNetToMatchGross(type, value, setFieldValue) {
  await setFieldValue('amount_gross', value);
  if (!showAmountNet(type)) {
    await setFieldValue('amount_net', value);
  }
}

const formQuery = '#transaction-form';

const App = () => {
  // State
  const [errorMsg, setErrorMsg] = useState('');
  const [showForm, setShowForm] = useState(true);

  // Handle error condition and prevent the form from being submitted multiple times
  useEffect(() => {
    if (errorLoadingConfig) {
      setErrorMsg(errorLoadingConfig);
      setShowForm(false);
    }
    if (typeof preventMultipleSubmit !== 'undefined') {
      preventMultipleSubmit(formQuery);
    }
  }, []);

  // Handlers
  const onSubmit = async (values) => {
    const result = (action === 'add')
      ? await API.add(endpointUrl, values, setErrorMsg)
      : await API.edit(endpointUrl, values, setErrorMsg);

    if (result) {
      window.location.href = '/admin/transactions';
      return;
    }

    cancelSubmit(formQuery);
  };
  const onDelete = async () => {
    if (confirm(`Are you sure you want to delete this transaction?`)) {
      const result = await API.delete(endpointUrl, setErrorMsg);
      if (result) {
        window.location.href = '/admin/transactions';
        return;
      }

      cancelSubmit(formQuery);
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
          {({values, setFieldValue}) => (
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
                      <option key={id} value={id}>
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
                    <p>{guidanceName(values.type)}</p>
                  </div>
                </div>

                <div className="required">
                  <label htmlFor="amount-gross">
                    Amount
                    {showAmountNet(values.type) && " (gross)"}
                  </label>
                </div>

                <div className="row">
                  <div className="col-sm-6">
                    <div className="form-group number required">
                      <Field className="form-control" type="number"
                             name="amount_gross" min="0" step="0.01"
                             required="required"
                             data-validity-message="This field cannot be left empty"
                             id="amount-gross"
                             aria-required="true"
                             onChange={(e) => {setNetToMatchGross(values.type, e.target.value, setFieldValue)}}
                      />
                    </div>
                  </div>
                  <div className="col-sm-6">
                    <p>
                      {guidanceAmountGross(values.type)}
                    </p>
                  </div>
                </div>

                <div style={!showAmountNet(values.type) ? {display: 'none'} : null}>
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
                               id="amount-net"
                               aria-required="true" />
                      </div>
                    </div>
                    <div className="col-sm-6">
                      <p>The total amount received (gross amount minus any processing fees)</p>
                    </div>
                  </div>
                </div>

                {showProjectSelector(values.type) && (
                  <>
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
                        <p>
                          {guidanceProject(values.type)}
                        </p>
                      </div>
                    </div>
                  </>
                )}

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
                      {guidanceMetadata(values.type)}
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
