import React from "react";
import { withRouter } from 'react-router-dom';
import Select from "react-select";
import DatePicker from "react-datepicker";
import ReactQuill from "react-quill";
import moment from 'moment';

import { Button, Modal, ModalBody, ModalFooter, ModalHeader, Form, Label, FormGroup, CustomInput, Col, Input } from "reactstrap";
import { PlusSquare, XSquare } from "react-feather";

import { GetDependenciesPlanLines, AddPlanLine, UpdatePlanLine } from '../../../controllers/plans';

class PlanLineForm extends React.Component {
  constructor(props) {
    super(props);

    let type;

    if (this.props.plan_line && this.props.plan_line.id) {
      type = 'Edit';
    } else {
      type = 'Add';
    }

    this.state = {
      formType: type,
      formName: 'Plan Line',
      newFormCount: 0,
      plan_id: this.props.match.params.id,
      plan_line: this.props.plan_line ? this.props.plan_line : {},
      plan_line_price: this.props.plan_line ? this.props.plan_line : {},
      id: null,
      productOpts: [],
      planLineOpts: [],
      planLineTypeOpts: [],
      forms: [],
      plan_lines: []
    };

    this.toggle = this.toggle.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
    this.formatter = this.formatter.bind(this);
    this.createForm = this.createForm.bind(this);
    this.addForm = this.addForm.bind(this);
  }

  toggle() {
    this.props.hide()
  }

  update(val) {
    this.props.update(val)
  }

  handleInputChange(e, count, name) {
    const planCount = this.formatter(count),
          { value } = e.target

    let plan_line = this.state[planCount],
    plan_lines = Object.assign({}, this.state.plan_lines);

    plan_line[name] = value;
    if (this.state.plan_lines[count] === undefined) {
      plan_lines[count] = {
        [name]: value
      }
    } else {
      plan_lines[count][name] = value
    }
    
    
    this.setState({
      plan_lines,
      [planCount]: plan_line
    })
  }

  handleSelectChange = (count, name, value) => {
    const planCount = this.formatter(count);
    let plan_line = this.state[planCount];

    plan_line[name] = value;
    if (this.state.plan_lines[count] === undefined) this.state.plan_lines[count] = {};
    this.state.plan_lines[count][name] = value.value;
    this.setState({ [planCount]: plan_line })
  }

  handleCheckChange(e, count, name) {
    const planCount = this.formatter(count),
          { checked } = e.target
    let plan_line = this.state[planCount],
        plan_lines = Object.assign({}, this.state.plan_lines);

    plan_line[name] = checked;
    if (this.state.plan_lines[count] === undefined) {
      this.state.plan_lines[count] = {}
    }
    plan_lines[count][name] = checked;

    this.setState({
      plan_lines,
      [planCount]: plan_line
    })
  }

  handleDatePickerChange(count, name, date) {
    const planCount = this.formatter(count)
    let plan_line = this.state[planCount]

    plan_line[name] = date;

    if (this.state.plan_lines[count] === undefined) {
      this.state.plan_lines[count] = {}
    }

    let plan_lines = Object.assign({}, this.state.plan_lines)

    plan_lines[count][name] = moment(date).format('DD-MM-YYYY')
    
    this.setState({
      [planCount]: plan_line,
      plan_lines
    })
  }

  handleQuillChange(val, count) {
    const planCount = this.formatter(count)

    let plan_line = this.state[planCount],
    plan_lines = Object.assign({}, this.state.plan_lines);

    plan_line.description_long = val;
    if (this.state.plan_lines[count] === undefined) {
      plan_lines[count] = {
        description_long: val
      }
    } else {
      plan_lines[count].description_long = val
    }
    
    
    this.setState({
      plan_lines,
      [planCount]: plan_line
    })
  }

  handleSubmit(e) {
    e.preventDefault();

    if (this.props.plan_line && this.props.plan_line.id) {
      this.state.plan_line.product,
      this.state.plan_line.parent_plan = null;
      this.state.plan_line._method = 'PATCH';

      (async () => {
        await UpdatePlanLine(this.props.id, this.state.plan_line)
          .then(res => {
            this.toggle()

            this.props.update()
          })
          .catch(err => {
            console.log(err)
          });
      })()
        .catch(err => {
          console.log(err)
        })
    } else {
      (async () => {
        await AddPlanLine(this.state.plan_id, { plan_lines: this.state.plan_lines })
          .then(res => {
            this.toggle()

            this.props.update()
          })
          .catch(err => {
            console.log(err)
          });
      })()
        .catch(err => {
          console.log(err)
        })
    }
  }

  formatter(count) {
    return count === 0 ? `plan_line` : `plan_line${count}`
  }

  createForm(count) {
    const forms = this.state.forms,
      planCount = this.formatter(count);

    this.setState({
      [planCount]: {
        product_id: null,
        plan_line_type: null,
        parent_plan_line_id: null,
        mandatory_line: false,
        plan_start: null,
        plan_stop: null,
        description: '',
        description_long: ''
      }
    })

    forms.push(count)

    this.setState({ forms })
  }

  addForm() {
    const count = parseInt(this.state.newFormCount) + 1
    this.setState({ newFormCount: count })
    this.createForm(count)
  }

  removeForm(index) {
    const planCount = this.formatter(index);
    let forms = this.state.forms,
      newFormCount = parseInt(this.state.newFormCount),
      youngerSiblings = 0;

    forms.splice(index, 1);

    if (index < newFormCount) {
      youngerSiblings = newFormCount - index;

      for (let x = index; x < index + youngerSiblings; x++) {
        const siblingCount = this.formatter(x + 1),
          newPlanCount = this.formatter(x);

        this.setState({
          [newPlanCount]: {
            product_id: this.state[siblingCount].product_id,
            plan_line_type: this.state[siblingCount].plan_line_type,
            parent_plan_line_id: this.state[siblingCount].parent_plan_line_id,
            mandatory_line: this.state[siblingCount].mandatory_line,
            plan_start: this.state[siblingCount].plan_start,
            plan_stop: this.state[siblingCount].plan_stop
          },
          [siblingCount]: {
            product_id: null,
            plan_line_type: null,
            parent_plan_line_id: null,
            mandatory_line: false,
            plan_start: null,
            plan_stop: null
          }
        })
      }
    } else {
      this.setState({
        [planCount]: {
          product_id: null,
          plan_line_type: null,
          parent_plan_line_id: null,
          mandatory_line: false,
          plan_start: null,
          plan_stop: null
        }
      })
    }

    this.setState({
      forms,
      newFormCount: newFormCount - 1
    })
  }

  componentDidMount() {
    (async () => {
      await GetDependenciesPlanLines()
        .then(res => {
          const datas = res.data,
            products = datas.products.data,
            plan_lines = datas.plan_lines.data,
            plan_line_types = datas.plan_line_types.data;

          let productOpt = [],
            planLineOpt = [],
            planLineTypes = [];

          products.forEach((product) => {
            productOpt.push({
              label: product.description,
              value: product.id
            });
          });

          plan_line_types.forEach((plan_line_type, ) => {
            planLineTypes.push({
              label: plan_line_type.line_type,
              value: plan_line_type.id
            })
          });
          
          plan_lines.forEach((plan_line) => {
            if (plan_line.product) {
              planLineOpt.push({
                label: plan_line.product.description,
                value: plan_line.id
              });
            }
          });

          this.setState({
            productOpts: productOpt,
            planLineTypeOpts: planLineTypes,
            planLineOpts: planLineOpt
          });

          this.createForm(0)

          if (this.props.plan_line && this.props.plan_line.id) {
            const plan = this.props.plan_line;

            this.setState({
              plan_id: plan.plan_id,
              product_id: {
                value: plan.product.id,
                label: plan.product.description
              },
              parent_plan_line_id: {
                value: plan.product.product_type.id,
                label: plan.product.product_type.type
              },
              plan_start: new Date(plan.plan_start),
              plan_stop: new Date(plan.plan_stop)
            });

            if (plan.plan_line_type) {
              this.setState({
                plan_line_type: {
                  value: plan.plan_line_type.id,
                  label: plan.plan_line_type.line_type
                }
              });
            }
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
        className="form-plan-lines"
        isOpen={this.props.show}
        toggle={this.toggle}
        centered
      >
        {this.state.forms !== [] ?
          <Form onSubmit={this.handleSubmit}>
            <ModalHeader className="modal-header-plan-lines">
              <span className="d-flex justify-content-between">
                <span>{this.state.formType} {this.state.formName}</span>
                <span className="btn btn-secondary btn-sm radius-5 p-0" onClick={this.addForm}><PlusSquare /></span>
              </span>
            </ModalHeader>

            {
              this.state.forms.map((i, x) => (
                <ModalBody className="m-3" key={x}>
                  {x !== 0 ?
                    <span className="remove-form btn btn-danger btn-sm radius-5 p-0" onClick={this.removeForm.bind(this, x)}><XSquare /></span> : null
                  }
                  <FormGroup>
                    <Label for="product_id">Product</Label>
                    {this.state.productOpts ?
                      <Select
                        id="product_id"
                        className="react-select-container"
                        classNamePrefix="react-select"
                        options={this.state.productOpts}
                        value={this.state[this.formatter(x)].product_id}
                        onChange={this.handleSelectChange.bind(this, x, 'product_id')}
                        maxMenuHeight="100"
                      /> : null
                    }
                  </FormGroup>

                  <FormGroup>
                    <Label for="plan_line_type">Plan line type</Label>
                    {this.state.planLineTypeOpts ?
                      <Select
                        id="plan_line_type"
                        className="react-select-container"
                        classNamePrefix="react-select"
                        options={this.state.planLineTypeOpts}
                        value={this.state[this.formatter(x)].plan_line_type}
                        onChange={this.handleSelectChange.bind(this, x, 'plan_line_type')}
                        maxMenuHeight="100"
                      /> : null
                    }
                  </FormGroup>

                  <FormGroup>
                    <Label for="parent_plan_line_id">Parent Plan Line</Label>
                    {this.state.planLineOpts ?
                      <Select
                        id="parent_plan_line_id"
                        className="react-select-container"
                        classNamePrefix="react-select"
                        options={this.state.planLineOpts}
                        value={this.state[this.formatter(x)].parent_plan_line_id}
                        onChange={this.handleSelectChange.bind(this, x, 'parent_plan_line_id')}
                        maxMenuHeight="100"
                      /> : null
                    }
                  </FormGroup>

                  <FormGroup>
                    <CustomInput
                      id={`${this.formatter(x)}_mandatory`}
                      type="checkbox"
                      name={`${this.formatter(x)}_mandatory_line`}
                      label="Mandatory Line"
                      onChange={ (e) => { this.handleCheckChange(e, x, 'mandatory_line') } }
                    />
                  </FormGroup>

                  <FormGroup className="row">
                    <Col className="pr-2 pl-0">
                      <Label for={`${this.formatter(x)}_plan_start`}>Plan Start</Label>
                      <DatePicker
                        id={`${this.formatter(x)}_plan_start`}
                        className="form-control"
                        name={`${this.formatter(x)}_plan_start`}
                        dateFormat="dd/MM/yyyy"
                        autoComplete="off"
                        selected={this.state[this.formatter(x)].plan_start ? new Date(this.state[this.formatter(x)].plan_start) : null}
                        onChange={this.handleDatePickerChange.bind(this, x, 'plan_start')}
                      />
                    </Col>
                    <Col className="pr-0 pl-2">
                      <Label for={`${this.formatter(x)}_plan_stop`}>Plan Stop</Label>
                      <DatePicker
                        id={`${this.formatter(x)}_plan_stop`}
                        className="form-control"
                        name={`${this.formatter(x)}_plan_stop`}
                        dateFormat="dd/MM/yyyy"
                        autoComplete="off"
                        selected={this.state[this.formatter(x)].plan_stop ? new Date(this.state[this.formatter(x)].plan_stop) : null}
                        onChange={this.handleDatePickerChange.bind(this, x, 'plan_stop')}
                      />
                    </Col>
                  </FormGroup>

                  <FormGroup>
                    <Label for={`${this.formatter(x)}_description`}>Description</Label>
                    <Input
                      id={`${this.formatter(x)}_description`}
                      name={`${this.formatter(x)}_description`}
                      value={ this.state[this.formatter(x)].description }
                      onChange={ (e) => { this.handleInputChange(e, x, 'description') } }
                    />
                  </FormGroup>

                  <FormGroup className="w-100">
                    <ReactQuill
                      placeholder='Long Description'
                      value={ this.state[this.formatter(x)].description_long }
                      onChange={ (e) => { this.handleQuillChange(e, x) } }
                    />
                  </FormGroup>
                </ModalBody>
              ))
            }

            <ModalFooter className="justify-content-between">
              <span className="btn btn-danger" onClick={this.toggle}>Cancel</span>
              <Button color="primary">Submit</Button>
            </ModalFooter>
          </Form> : null
        }
      </Modal>
    );
  }
}

export default withRouter(PlanLineForm);
