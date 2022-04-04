import React from "react";
import { withRouter } from 'react-router-dom';
import Select from "react-select";
import DatePicker from "react-datepicker";
import ReactQuill from "react-quill";
import moment from 'moment';

import { Label, FormGroup, Row, Col, Modal, ModalBody, ModalFooter, ModalHeader, Button, Input, CustomInput, Form } from "reactstrap";
import { PlusSquare, XSquare } from "react-feather";

import { GetProductsList } from '../../../controllers/products';
import { GetPlanSubscriptionLineTypes, AddSubscriptionLine, UpdateSubscriptionLine } from '../../../controllers/subscriptions';

class SubscriptionLineForm extends React.Component {
    constructor(props) {
        super(props)

        this.state = {
            formType: null,
            formName: 'Subscription Line',
            id: null,
            subscription_id: this.props.id,
            subscription_line_type: null,
            plan_id: null,
            mandatory_line: false,
            subscription_start: null,
            subscription_stop: null,
            serial: null,
            product: null,
            description: '',
            description_long: '',
            products: [],
            lineTypes: []
        }
        this.toggle = this.toggle.bind(this);
        this.handleQuillChange = this.handleQuillChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    toggle() {
        this.props.hide()
    }

    update(val) {
        this.props.update(val)
    }

    handleSelectChange = (name, data) => {
        let arr = {};
        arr[name] = data;
        this.setState(arr);
    }

    handleInput(e) {
        const { name, value } = e.target

        this.setState({ [name]: value })
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
        e.preventDefault()

        const subscriptionLine = {
            // plan_line_id: null,
            // serial: null,
            // mandatory_line: 0,
            description: this.state.description,
            description_long: this.state.description_long,
            product_id: this.state.product ? this.state.product.value : null,
            subscription_line_type: this.state.subscription_line_type.value,
            subscription_id: this.props.subscription_id,
            subscription_start: moment(this.state.subscription_start).format('DD-MM-YYYY'),
            subscription_stop: moment(this.state.subscription_stop).format('DD-MM-YYYY')
        }

        if (this.props.selectedData) {
            subscriptionLine._method = 'PATCH';
            (async () => {
                await UpdateSubscriptionLine(this.props.id, subscriptionLine)
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
                await AddSubscriptionLine(this.state.subscription_id, subscriptionLine)
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
        (async () => {
            await Promise.all([
                GetProductsList()
                    .then(res => {
                        const products = res.data.data;
                        let productLists = [];
                        products.forEach((product) => {
                            productLists.push({
                                label: product.description,
                                value: product.id
                            })
                        })
                        this.setState({ products: productLists });
                    })
                    .catch(err => {
                        console.log('GetProductsList', err)
                    }),
                GetPlanSubscriptionLineTypes()
                    .then(res => {
                        const lineTypes = res.data.data;
                        let lineTypeLists = [];

                        lineTypes.forEach((lineType) => {
                            lineTypeLists.push({
                                label: lineType.line_type,
                                value: lineType.id
                            });
                        })
                        this.setState({ lineTypes: lineTypeLists });
                    })
                    .catch(err => {
                        console.log(err)
                    }),
            ])

            if (this.props.selectedData) {
                this.setState({ formType: 'Edit' })
                const subscriptionLine = this.props.data
                let lineTypeObj = {}
                let productObj = {}

                if (subscriptionLine.line_type) {
                    lineTypeObj = {
                        label: subscriptionLine.line_type.line_type,
                        value: subscriptionLine.line_type.id
                    }
                }

                if (subscriptionLine.product) {
                    productObj = {
                        label: subscriptionLine.product.description,
                        value: subscriptionLine.product.id
                    }
                }

                this.setState({
                    id: subscriptionLine.id,
                    subscription_id: subscriptionLine.subscription_id,
                    subscription_line_type: lineTypeObj,
                    plan_line_id: subscriptionLine.plan_line_id,
                    product_id: subscriptionLine.product_id,
                    product: productObj,
                    serial: subscriptionLine.serial,
                    mandatory_line: subscriptionLine.mandatory_line,
                    subscription_start: new Date(subscriptionLine.subscription_start),
                    subscription_stop: new Date(subscriptionLine.subscription_stop)
                })
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
                <Form onSubmit={this.handleSubmit}>
                    <ModalHeader>
                        <span>{this.state.formType} Subscription Line</span>
                    </ModalHeader>
                    <ModalBody key="0" className="mt-3 mb-3">
                        <Row>
                            <Col md={2}>Subscription Line:</Col>
                            <Col md={10}>
                                <FormGroup className="row">
                                    <Col md={6}>
                                        <Select
                                            name="product"
                                            options={this.state.products}
                                            className="react-select-container"
                                            classNamePrefix="react-select"
                                            placeholder="Product"
                                            value={this.state.product}
                                            onChange={this.handleSelectChange.bind(this, 'product')}
                                            maxMenuHeight="100"
                                        />
                                    </Col>
                                    
                                    <Col md={6}>
                                        <Select
                                            name="subscription_line_type"
                                            options={this.state.lineTypes}
                                            placeholder="Line Type"
                                            className="react-select-container"
                                            classNamePrefix="react-select"
                                            value={this.state.subscription_line_type}
                                            onChange={this.handleSelectChange.bind(this, 'subscription_line_type')}
                                            maxMenuHeight="100"
                                        />
                                    </Col>
                                </FormGroup>
                                <FormGroup className="row">
                                    <Col md={3}>
                                        <DatePicker
                                            id="subscription_start"
                                            className="form-control"
                                            name="subscription_start"
                                            dateFormat="dd/MM/yyyy"
                                            autoComplete="off"
                                            placeholderText="Start"
                                            selected={this.state.subscription_start}
                                            selectsStart
                                            startDate={this.state.subscription_start}
                                            endDate={this.state.subscription_stop}
                                            onChange={this.handleDatePickerChange.bind(this, 'subscription_start')}
                                        />
                                    </Col>
                                    <Col md={3}>
                                        <DatePicker
                                            id="subscription_stop"
                                            className="form-control"
                                            name="subscription_stop"
                                            dateFormat="dd/MM/yyyy"
                                            autoComplete="off"
                                            placeholderText="Stop"
                                            selected={this.state.subscription_stop}
                                            selectsEnd
                                            startDate={this.state.subscription_start}
                                            endDate={this.state.subscription_stop}
                                            onChange={this.handleDatePickerChange.bind(this, 'subscription_stop')}
                                        />
                                    </Col>
                                    <Col md={6}>
                                        <Input
                                            name="description"
                                            placeholder="Description"
                                            value={this.state.description}
                                            onChange={(e) => this.handleInput(e)}
                                        />
                                    </Col>
                                </FormGroup>
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
                        <Button className="btn btn-danger" onClick={this.toggle} data-dismiss="modal">Cancel</Button>
                        <Button color="primary">Submit</Button>
                    </ModalFooter>
                </Form>
            </Modal >
        );
    }
}

export default withRouter(SubscriptionLineForm);
