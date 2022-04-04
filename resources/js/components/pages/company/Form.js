import React from "react";
import { withRouter } from 'react-router-dom';
import Select from "react-select";

import { Button, Modal, ModalBody, ModalFooter, ModalHeader, Label, FormGroup } from "reactstrap";
import { AvForm, AvField, AvGroup } from "availity-reactstrap-validation";
import { toastr } from "react-redux-toastr";
import Loader from '../../components/Loader';

import { GetCompany, AddCompany, UpdateCompany } from '../../controllers/companies';

class CompanyForm extends React.Component {
  constructor(props) {
    super(props);

    const path = window.location.href.split('/');

    this.state = {
      formType: null,
      formName: 'Company',
      // companies: [],
      id: null,
      parent_id: null,
      // parent_company: {
      //   value: null,
      //   label: null
      // },
      billing_day: null,
      loading: true
    };

    this.name = React.createRef();
    this.toggle = this.toggle.bind(this);
    this.handleInputChange = this.handleInputChange.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
  }

  showToastr(type, title, msg) {
    const opts = {
      timeOut: 5000,
      showCloseButton: false,
      progressBar: false
    };

    const toastrInstance =
      type === 201 ? toastr.success
        : toastr.error;

    toastrInstance(title, msg, opts);
  }


  toggle() {
    this.props.hide()
  }

  handleSelectChange = (name, value) => {
    let change = {};
    change[name] = value;
    this.setState(change);
  }

  handleInputChange(e) {
    this.setState({ [e.target.name]: e.target.value });
    e.persist();
  }

  handleSubmit(e) {
    e.persist();
    if (this.state.id) {
      (async () => {
        const getCompany = {
          _method: 'PATCH',
          id: this.state.id,
          name: this.state.name,
          parent_id: this.state.id === 1 ? null : this.state.parent_company.value,
          billing_day: this.state.billing_day
        }

        await UpdateCompany(this.props.id, getCompany)
          .then(res => {
            this.props.update(res.data.data)
          })
          .catch(err => {
            this.showToastr(err.response.status, 'Error', err.response.data.message);
          });
      })()
        .catch(err => {
          console.log(err)
        })
    } else {
      if (this.state.name !== null && this.state.parent_company.value !== null) {
        (async () => {
          const newCompany = {
            name: this.state.name,
            parent_id: this.state.parent_company.value,
            billing_day: this.state.billing_day
          }

          await AddCompany(newCompany)
            .then(res => {
              this.props.history.push({ pathname: `/company/${res.data.data.id}/details` })
            })
            .catch(err => {
              this.showToastr(err.response.status, 'Error', err.response.data.message);
            });
        })()
          .catch(err => {
            console.log(err)
          })
      } else {
        return false;
      }
    }
  }


  componentDidMount() {
    (async () => {
      let companyOpts = [];

      // await GetCompanyLists()
      //   .then(res => {
      //     const companies = res.data.data;

      //     companies.forEach((company) => {
      //       companyOpts.push({
      //         label: company.name,
      //         value: company.id
      //       });
      //     });

      //     this.setState({
      //       companyOpts
      //     });
      //   })
      //   .catch(err => {
      //     this.showToastr(err.response.status, 'Error', err.response.data.message);
      //   });

      if (this.props.id) {
        (async () => {
          await GetCompany(this.props.id)
            .then(res => {
              const company = res.data.data;
              const selectedRootCompany = companyOpts.filter(v => v.value === company.parent_id);

              this.setState({
                parent_company: selectedRootCompany.length > 0 ? selectedRootCompany[0] : null,
                name: company.name,
                id: company.id,
                parent_id: selectedRootCompany.length > 0 ? selectedRootCompany[0].value : null,
                billing_day: selectedRootCompany.length > 0 ? company.billing_day : null,
                formType: 'Edit',
                loading: false
              });
            })
            .catch(err => {
              this.showToastr(err.response.status, 'Error', err.response.data.message);
            });
        })()
          .catch(err => {
            console.log(err)
          })
      } else {
        this.setState({
          formType: 'Add',
          loading: false
        })
      }
    })()
      .catch(err => {
        console.log(err)
      })
  }

  render() {
    return (
      <Modal
        isOpen={this.props.show}
        toggle={this.toggle}
        centered
      >
        {!this.state.loading ?
          <AvForm onSubmit={this.handleSubmit}>
            <ModalHeader>{this.state.formType} {this.state.formName}</ModalHeader>
            <ModalBody className="m-3">
              <AvGroup>
                <AvField name="name" label="Name" value={this.state.name} onChange={this.handleInputChange} required ref={this.props.inputElement} />
              </AvGroup>
              <FormGroup>
                <Label for="parent_company">Parent Company</Label>
                {this.state.companyOpts ?
                  <Select
                    id="parent_company"
                    className="react-select-container"
                    classNamePrefix="react-select"
                    options={this.state.companyOpts}
                    value={this.state.parent_company}
                    onChange={this.handleSelectChange.bind(this, 'parent_company')}
                    maxMenuHeight="100"
                  /> : null
                }
              </FormGroup>
              <AvGroup>
                <AvField name="billing_day" label="Invoice Billing day (day of the month when invoice are processed, e.g. every 15th of the month)" value={this.state.billing_day} onChange={this.handleInputChange} required ref={this.props.inputElement} />
              </AvGroup>
            </ModalBody>
            <ModalFooter className="justify-content-between">
              <span className="btn btn-danger" onClick={this.toggle}>Cancel</span>
              <Button color="primary">Submit</Button>
            </ModalFooter>
          </AvForm>
          : <Loader />
        }
      </Modal>
    );
  }
}

export default withRouter(CompanyForm);
