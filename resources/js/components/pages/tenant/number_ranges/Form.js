import React from "react";
import { withRouter } from 'react-router-dom';
import Select from "react-select";
import DatePicker from "react-datepicker";
import ReactQuill from "react-quill";
import moment from 'moment';
import _ from 'lodash';


import { Button, Modal, ModalBody, ModalFooter, ModalHeader, Label, FormGroup, Row, Col, Form, CustomInput } from "reactstrap";
import { AvForm, AvInput } from "availity-reactstrap-validation";

import { PlusSquare, XSquare } from "react-feather";

import { GetNumberRange, AddNumberRange, UpdateNumberRange } from '../../../controllers/number_ranges';

const numberRangeTypeOpts = [
    { value: "invoice_no", label: "Invoice Number" },
    { value: "customer_number", label: "Customer Number" },
    { value: "journal_no", label: "Journal Number" },
    { value: "subscription_no", label: "Subscription Number" },
  ];
  

class NumberRangeForm extends React.Component {
    constructor(props) {
        super(props)

        this.state = {
            formType: null,
            formName: 'Number Range',
            id: this.props.id,
            type: null,
            description: null,
            start: null,
            end: null,
            format: null,
            randomized: null,
            current: null,
            numberRange: this.props.selectedData ? this.props.selectedData : {},
        }
        this.toggle = this.toggle.bind(this);
        this.handleQuillChange = this.handleQuillChange.bind(this);
        this.handleInputChange = this.handleInputChange.bind(this);
        this.handleChange = this.handleChange.bind(this);
        this.handleCheckChange = this.handleCheckChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    toggle() {
        this.props.hide()
    }

    update(val) {
        this.props.update(val)
    }

    handleChange(parent, e) {
        if (parent) {
            this.setState({
                numberRange : { 
                    ...this.state.numberRange,
                    [e.target.name]: e.target.value
                }
            });
        } else { 
            this.setState({ 
                [e.target.name]: e.target.value,
            });
        }
        
        console.log( JSON.stringify(this.state));
    }

    handleSelectChange = (name, data) => {
        let arr = {};
        arr[name] = data;
        this.setState(arr);
        e.persist();
    }

    handleInput(e) {
        const { name, value } = e.target
        this.setState({ [name]: value });
        e.persist();
    }

    handleInputChange(e) {
        this.setState({ [e.target.name]: e.target.value });
        e.persist();
    }

    handleCheckChange(e) {
        this.setState({ [e.target.name]: e.target.value == "on" });
        e.persist();
    }

    handleDatePickerChange(name, date) {
        let arr = {};
        arr[name] = date;

        this.setState(arr);
    }

    handleQuillChange(val) {
        this.setState({ description_long: val })
    }

    handleSubmit(e) {
        e.preventDefault();

        const formParams = {
            type: this.state.type.value,
            description: this.state.description,
            start: this.state.start,
            end: this.state.end,
            format: this.state.format,
            current: parseInt(this.state.current),
            randomized: this.state.randomized
        };
        

        if (this.props.selectedData) {
            formParams._method = 'PATCH';
            (async () => {
                
                await UpdateNumberRange(this.state.id, formParams)
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
                await AddNumberRange(this.state.number_range_id, formParams)
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

    componentDidMount() {
        const numberRangeDataParam = this.props.selectedData;
    
        (async () => {
            await Promise.all([
                GetNumberRange(null, numberRangeDataParam.id)
                    .then(res => { 
                        const data = res.data.data;
                        this.setState({ numberRange: data });
                    })
                    .catch(err => {
                        console.log('GetNumberRange', err)
                    }),
            ])

            if (numberRangeDataParam) {
                this.setState({ formType: 'Edit' });
                this.setState({
                    id: numberRangeDataParam.id,
                    number_range_id: numberRangeDataParam.subscription_id,
                    type: _.filter(numberRangeTypeOpts, { value: numberRangeDataParam.type})[0],
                    description: numberRangeDataParam.description,
                    start: numberRangeDataParam.start,
                    end: numberRangeDataParam.end,
                    format: numberRangeDataParam.format,
                    randomized: numberRangeDataParam.randomized === "1",
                    current: numberRangeDataParam.current,
                    numberRange: numberRangeDataParam,
                });
            } else {
                this.setState({ formType: 'Add' })
            }
        })()
            .catch(err => {
                console.log(err)
            })
    }

    render() {
        return (
            <Modal
                className="form-subscription-lines"
                isOpen={this.props.show}
                toggle={this.toggle}
                centered
            >
                <AvForm onSubmit={this.handleSubmit}>
                    <ModalHeader>
                        <span>{this.state.formType} Number Range</span>
                    </ModalHeader>
                    <ModalBody key="0" className="mt-3 mb-3">
                        <Row className="p-2">
                            <Col md={2}>Description:</Col>
                            <Col md={10}>
                                <AvInput 
                                    name="description" 
                                    placeholder="Description" 
                                    value={ this.state.description || '' } 
                                    onChange={ this.handleInputChange } 
                                />
                            </Col>
                        </Row>
                        <Row className="p-2">
                            <Col md={2}>Format:</Col>
                            <Col md={10}>
                                <AvInput 
                                    name="format" 
                                    placeholder="Format" 
                                    value={ this.state.format || '' } 
                                    onChange={ this.handleInputChange } 
                                />
                            </Col>
                        </Row>
                        <Row className="p-2">
                            <Col md={2}>Type:</Col>
                            <Col md={10}>
                                <Select
                                    className="react-select-container react-select-lg"
                                    classNamePrefix="react-select"
                                    options={ numberRangeTypeOpts }
                                    value={ this.state.type || '' } 
                                    onChange={this.handleSelectChange.bind( this, 'type' )}
                                    maxMenuHeight="100"
                                />
                            </Col>
                        </Row>
                        <Row className="p-2">
                            <Col md={2}>Start:</Col>
                            <Col md={10}>
                                <AvInput 
                                    name="start" 
                                    placeholder="Start" 
                                    value={ this.state.start || '' } 
                                    onChange={ this.handleInputChange } 
                                />
                            </Col>
                        </Row>
                        <Row className="p-2">
                            <Col md={2}>End:</Col>
                            <Col md={10}>
                                <AvInput 
                                    name="end" 
                                    placeholder="End" 
                                    value={ this.state.end || '' } 
                                    onChange={ this.handleInputChange } 
                                />
                            </Col>
                        </Row>
                        <Row className="p-2">
                            <Col md={2}>&nbsp;</Col>
                            <Col md={10}>
                                <CustomInput 
                                    id="randomized"
                                    type="checkbox"
                                    label="Generate random number"
                                    name="randomized"
                                    defaultChecked={ this.state.randomized }
                                    onChange={ (e) => { this.handleCheckChange(e) } }
                                    />
                            </Col>
                        </Row>

                        <Row className="p-2">
                            <Col md={2}>Last saved number:</Col>
                            <Col md={10}>
                                <AvInput 
                                    name="current" 
                                    placeholder="Last saved number" 
                                    value={ this.state.current || '' } 
                                    onChange={ this.handleInputChange } 
                                />
                            </Col>
                        </Row>
                    </ModalBody>
                    <ModalFooter className="justify-content-between">
                        <Button className="btn btn-danger" onClick={this.toggle} data-dismiss="modal">Cancel</Button>
                        <Button color="primary">Submit</Button>
                    </ModalFooter>
                </AvForm>
            </Modal >
        );
    }
}

export default withRouter(NumberRangeForm);
