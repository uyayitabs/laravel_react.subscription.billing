import React from "react";
import { withRouter } from 'react-router-dom';
import Select from "react-select";
import DatePicker from "react-datepicker";
import ReactQuill from "react-quill";
import moment from 'moment';

import { Button, Modal, ModalBody, ModalFooter, ModalHeader, Label, FormGroup, Row, Col, Input } from "reactstrap";
import { AvForm, AvField, AvGroup } from "availity-reactstrap-validation";

import { AddPlan, UpdatePlan, GetDependenciesPlans } from '../../controllers/plans';

class PlanForm extends React.Component {
  constructor(props) {
    super(props);

    const path = window.location.href.split('/');
    const id = path[path.length - 2]

    this.state = {
      formType: null,
      formName: 'Plan',
      id: id,
      area_code_id: null,
      description: '',
      description_long: '',
      plan_start: null,
      plan_stop: null,
      areaCodeOpts: null,
      planOpts: null,
      parent_plan: null,
      plan: {}
    };

    this.toggle = this.toggle.bind(this);
    this.handleInputChange = this.handleInputChange.bind(this);
    this.handleQuillChange = this.handleQuillChange.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
  }

  toggle() {
    this.props.hide()
  }

  update(val) {
    this.props.update(val)
  }

  handleSelectChange(name, value) {
    let plan = Object.assign({}, this.state.plan)

    plan[name] = value.value

    this.setState({
      plan,
      [name]: value
    })
  }

  handleInputChange(e) {
    let plan = Object.assign({}, this.state.plan)
    const { name, value } = e.target

    plan[name] = value
    this.setState({ plan })
      
    e.persist();
  }

  handleQuillChange(val) {
    let plan = Object.assign({}, this.state.plan)

    plan.description_long = val
    this.setState({
      plan,
      description_long: val
    })
  }

  handleDatePickerChange(name, date) {
    let plan = Object.assign({}, this.state.plan)

    plan[name] = moment(date).format('DD-MM-YYYY')
    
    this.setState({
      plan,
      [name]: date
    })
  }

  handleSubmit(e) {
    e.persist();

    (async () => {
      await AddPlan(this.state.plan)
        .then(res => {
          const getId = res.data.data.id;

          this.props.history.push({ pathname: `/plans/${getId}/details` })
        })
        .catch(err => {
          console.log(err)
        });
    })()
      .catch(err => {
        console.log(err)
      })

    // if (!isNaN(this.state.id)) {
    //   this.state.plan._method = 'PATCH';
    //   this.state.plan.id = this.state.id;

    //   (async () => {
    //     await UpdatePlan(this.props.id, this.state.plan)
    //       .then(res => {
    //         this.toggle()

    //         this.update(res.data.data)
    //       })
    //       .catch(err => {
    //         console.log(err)
    //       });
    //   })()
    //     .catch(err => {
    //       console.log(err)
    //     })
    // } else {

    // }
  }

  componentDidMount() {
    (async () => {
      await GetDependenciesPlans()
        .then(res => {
          const data = res.data;

          this.setState({ formType: "Add New" })

          let areaCodeOpts = [],
            planOpts = [];

          data.area_codes.data.forEach((area_code, index) => {
            areaCodeOpts.push({
              label: area_code.id,
              value: area_code.id
            });
          });

          this.setState({ areaCodeOpts })

          data.plans.data.forEach((plan, ) => {
            let is_current = this.props.details && this.props.details.id == plan.id;

            if (!is_current) {
              planOpts.push({
                label: plan.description,
                value: plan.id
              });
            }
          });

          this.setState({ planOpts })

          if (this.props.details) {
            this.setState({ formType: 'Edit' })
            const details = this.props.details;

            this.setState({
              description: details.description,
              description_long: details.description_long
            })

            if (details.plan_start) {
              this.setState({ plan_start: new Date(details.plan_start) })
            }

            if (details.plan_stop) {
              this.setState({ plan_stop: new Date(details.plan_stop) })
            }

            if (details.parent) {
              this.setState({
                parent_plan: {
                  value: details.parent.id,
                  label: details.parent.description
                }
              })
            }

            this.setState({ plan: this.props.details });
          }
        })
        .catch(err => {
          console.log(err)
        });
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
        backdrop="static"
      >
        <AvForm onSubmit={this.handleSubmit}>
          <ModalHeader>{this.state.formType} {this.state.formName}</ModalHeader>
          <ModalBody className="mt-3 mb-3">
            <Row>
              <Col md={2}>Plan:</Col>
              <Col md={10}>
                <FormGroup className="row">
                  <Col>
                    {this.state.areaCodeOpts ?
                      <Select
                        id="area_code_id"
                        className="react-select-container"
                        classNamePrefix="react-select"
                        placeholder="Area Code"
                        options={this.state.areaCodeOpts}
                        value={this.state.area_code_id}
                        onChange={this.handleSelectChange.bind(this, 'area_code_id')}
                        maxMenuHeight="100"
                      /> : null
                    }
                  </Col>
                  <Col>
                    {this.state.planOpts ?
                      <Select
                        id="parent_plan"
                        className="react-select-container"
                        classNamePrefix="react-select"
                        placeholder="Parent plan"
                        options={this.state.planOpts}
                        value={this.state.parent_plan}
                        onChange={this.handleSelectChange.bind(this, 'parent_plan')}
                        maxMenuHeight="100"
                      /> : null
                    }
                  </Col>
                </FormGroup>
                {/* <AvGroup>
                  <AvField name="plan_type" placeholder="Plan Type" value={this.state.plan.plan_type} onChange={this.handleInputChange} />
                </AvGroup> */}

                <FormGroup className="row">
                  <Col>
                    <DatePicker
                      id="plan_start"
                      className="form-control"
                      name="plan_start"
                      dateFormat="dd/MM/yyyy"
                      autoComplete="off"
                      placeholderText="Plan Start"
                      selected={this.state.plan_start}
                      onChange={this.handleDatePickerChange.bind(this, 'plan_start')}
                    />
                  </Col>
                  <Col>
                    <DatePicker
                      id="plan_stop"
                      className="form-control"
                      name="plan_stop"
                      dateFormat="dd/MM/yyyy"
                      autoComplete="off"
                      placeholderText="Plan Stop"
                      selected={this.state.plan_stop}
                      onChange={this.handleDatePickerChange.bind(this, 'plan_stop')}
                    />
                  </Col>
                </FormGroup>

                <AvGroup>
                  <AvField 
                    name="description" 
                    placeholder="Description" 
                    value={this.state.plan.description} 
                    onChange={this.handleInputChange} 
                  />
                </AvGroup>

                <FormGroup className="row">
                    <Col>
                        <ReactQuill
                            placeholder='Long Description'
                            value={this.state.description_long}
                            onChange={this.handleQuillChange}
                        />
                    </Col>
                </FormGroup>
              </Col>
            </Row>
          </ModalBody>
          <ModalFooter className="justify-content-between">
            <span className="btn btn-danger" onClick={this.toggle}>Cancel</span>
            <Button color="primary">Submit</Button>
          </ModalFooter>
        </AvForm>
      </Modal>
    );
  }
}

export default withRouter(PlanForm);
