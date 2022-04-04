import React from "react";
import { withRouter } from 'react-router-dom';
import Select from "react-select";
import DatePicker from "react-datepicker";
import ReactQuill from "react-quill";
import moment from 'moment';

import { Button, Modal, ModalBody, ModalFooter, ModalHeader, Label, FormGroup, Row, Col, Table } from "reactstrap";
import { AvForm, AvInput } from "availity-reactstrap-validation";

import { GetPlanSubscriptionLineTypes, AddSubscription, UpdateSubscription } from '../../controllers/subscriptions'
import { GetRelationList } from '../../controllers/relations';
import { GetPlanList, GetPlan } from '../../controllers/plans';

class SubscriptionForm extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            formType: null,
            formName: 'Subscription',
            id: null,
            relation: null,
            relations: [],
            type: null,
            subscription_start: null,
            subscription_stop: null,
            plan: null,
            plans: [],
            lineTypes: [],
            planLines: null,
            description: null,
            description_long: null,
        };

        this.toggle = this.toggle.bind(this);
        this.handleInputChange = this.handleInputChange.bind(this);
        this.handleQuillChange = this.handleQuillChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handlePlanChange = this.handlePlanChange.bind(this);
        this.getFullname = this.getFullname.bind(this);
        this.getAddress = this.getAddress.bind(this);
    }

    toggle() {
        this.props.hide()
    }

    update(val) {
        this.props.update(val)
    }

    handleSelectChange = (name, value) => {
        let arr = {};
        arr[name] = value;
        this.setState(arr);
    }

    handleInputChange(e) {
        this.setState({ [e.target.name]: e.target.value });
        e.persist();
    }

    handleDatePickerChange(name, date) {
        let arr = {};
        arr[name] = date;

        this.setState(arr);
    }

    handlePlanChange = async (name, data) => {
        let arr = {};
        arr[name] = data;
        this.setState(arr);

        (async () => {
            await GetPlan(null, data.value)
                .then(res => {
                    const plan = res.data.data;

                    this.setState({ planLines: plan.plan_lines });
                })
                .catch(err => {
                    console.log(err)
                });
        })()
            .catch(err => {
                console.log(err)
            })
    }

    handleQuillChange(val) {
        this.setState({ description_long: val })
    }

    handleSubmit(e) {
        e.persist();

        let body = {
            id: this.state.id,
            relation_id: this.state.relation ? this.state.relation.value : null,
            plan_id: this.state.plan ? this.state.plan.value : null,
            type: this.state.type,
            description: this.state.description,
            description_long: this.state.description_long,
            subscription_start: moment(this.state.subscription_start).format('DD-MM-YYYY'),
            subscription_stop: moment(this.state.subscription_stop).format('DD-MM-YYYY')
        }

        if (this.state.id != undefined) {
            (async () => {
                body._method = 'PATCH'
                await UpdateSubscription(this.props.data.id, body)
                    .then(res => {
                        this.toggle()

                        const val = [{
                            id: this.state.id,
                            relation_id: this.state.relation.value,
                            plan_id: this.state.plan.value,
                            type: this.state.type,
                            description: this.state.description,
                            description_long: this.state.description_long,
                            subscription_start: moment(this.state.subscription_start).format('DD-MM-YYYY'),
                            subscription_stop: moment(this.state.subscription_stop).format('DD-MM-YYYY'),
                            subscriptionLines: this.state.subscriptionLines
                        }]

                        this.update(val);
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
                await AddSubscription(body)
                    .then(res => {
                        const getId = res.data.data.id;
                        this.props.history.push({ pathname: `/subscriptions/${getId}/details` })
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

    getFullname(person) {
        return person.first_name + ' ' + (person.middle_name ? person.middle_name : '') + ' ' + person.last_name;
    }

    getAddress(address) {
        return address.street1 + ' ' +
            (address.street2 ? address.street2 : '') + ' ' +
            address.city + ' ' +
            address.zipcode + ' ' +
            (address.country ? address.country.name : '');
    }

    componentDidMount() {
        const { id, data } = this.props;

        if (data) {

            const subscription = data
            let relationObj = {}
            let planObj = {}

            const relation = subscription.relation
            const plan = subscription.plan

            if (subscription.relation) {
                relationObj = {
                    value: relation.id,
                    label: relation.customer_number
                }
            }

            if (subscription.plan) {
                planObj = {
                    value: plan.id,
                    label: plan.description_long
                }
            }

            this.setState({
                formType: 'Edit',
                id: subscription.id,
                relation: relationObj,
                plan: planObj,
                type: subscription.type,
                subscription_start: new Date(subscription.subscription_start),
                subscription_stop: new Date(subscription.subscription_stop)
            })

        } else {
            this.setState({ formType: 'Add New' })
        }

        (async () => {
            await Promise.all([
                GetRelationList()
                    .then(res => {
                        const data = res.data.data;
                        let relations = [];

                        data.forEach((relation) => {
                            relations.push({
                                label: relation.name,
                                value: relation.id
                            });
                        })
                        this.setState({ relations })

                        if (id) {
                            const getIndex = relations.findIndex(item => parseInt(item.value) === parseInt(id));

                            this.setState({ relation: relations[getIndex] })
                        }
                    })
                    .catch(err => {
                        console.log(err)
                    }),
                GetPlanList()
                    .then(res => {
                        const plans = res.data.data;
                        let planNames = [];
                        plans.forEach((plan) => {
                            planNames.push({
                                label: plan.name,
                                value: plan.id
                            });
                        })
                        this.setState({ plans: planNames });
                    })
                    .catch(err => {
                        console.log(err)
                    }),
                GetPlanSubscriptionLineTypes()
                    .then(res => {
                        const lineTypes = res.data.data;
                        let lineTypeNames = [];
                        lineTypes.forEach((lineType) => {
                            lineTypeNames.push({
                                label: lineType.line_type,
                                value: lineType.id
                            });
                        })
                        this.setState({ lineTypes: lineTypeNames });
                    })
                    .catch(err => {
                        console.log(err)
                    })
            ])
        })()
            .catch(err => {
                console.log(err)
            })
    }

    render() {
        const Customer = () => {
            return (
                <div>
                    <p className="mb-1">
                        {this.props.relation.customer_number} / {this.getFullname(this.props.relation.persons[0])}
                    </p>
                    <p className="mb-1">
                        {this.getAddress(this.props.relation.addresses[0])}
                    </p>
                </div>
            )
        }

        return (
            <Modal
                isOpen={this.props.show}
                toggle={this.toggle}
                centered
                className="modal-lg"
                backdrop="static"
            >
                <AvForm onSubmit={this.handleSubmit}>
                    <ModalHeader>{this.state.formType} {this.state.formName}</ModalHeader>
                    <ModalBody className="mt-3 mb-3">
                        <Row>
                            <Col md={2}>Subscription:</Col>
                            <Col md={10}>
                                <FormGroup className="row">
                                    <Col xs="3">
                                        {
                                            this.props.relation ?
                                                <Customer />
                                                :
                                                <Select
                                                    required
                                                    id="relation"
                                                    className="react-select-container"
                                                    placeholder="Relation"
                                                    classNamePrefix="react-select"
                                                    options={this.state.relations}
                                                    value={this.state.relation}
                                                    onChange={this.handleSelectChange.bind(this, 'relation')}
                                                    maxMenuHeight="100"
                                                />
                                        }
                                    </Col>
                                    <Col xs="3">
                                        <AvInput id="type" name="type" placeholder="Type" value={this.state.type} required onChange={this.handleInputChange} />
                                    </Col>
                                    <Col xs="3">
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
                                    <Col xs="3">
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
                                </FormGroup>
                                <FormGroup className="row">
                                    <Col xs="6">
                                        <Select
                                            id="plan"
                                            className="react-select-container"
                                            classNamePrefix="react-select"
                                            placeholder="Plan"
                                            options={this.state.plans}
                                            value={this.state.plan}
                                            onChange={this.handlePlanChange.bind(this, 'plan')}
                                            maxMenuHeight="100"
                                        />
                                    </Col>
                                    <Col xs="6">
                                        <AvInput id="description" name="description" placeholder="Description" value={this.state.description} required onChange={this.handleInputChange} />
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
                        {this.state.planLines ?
                            <FormGroup>
                                <h5 className="mt-5">Plan Lines</h5>
                                <Table>
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Product Type</th>
                                            <th>Line Type</th>
                                            <th>Start</th>
                                            <th>Stop</th>
                                            <th>Fixed Price</th>
                                            <th>Margin</th>
                                            <th>Price Valid From</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {
                                            this.state.planLines.map((planLine, x) => (
                                                <tr key={x}>
                                                    <td>{planLine.product ? planLine.product.description : ''}</td>
                                                    <td>{planLine.product && planLine.product.product_type ? planLine.product.product_type.type : ''}</td>
                                                    <td>{planLine.plan_line_type ? planLine.plan_line_type.line_type : ''}</td>
                                                    <td>{planLine.plan_start}</td>
                                                    <td>{planLine.plan_stop}</td>
                                                    <td>{planLine.plan_line_price ? planLine.plan_line_price.fixed_price : ''}</td>
                                                    <td>{planLine.plan_line_price ? planLine.plan_line_price.margin : ''}</td>
                                                    <td>{planLine.plan_line_price ? moment(planLine.plan_line_price.price_valid_from).format('MMM D, Y') : ''}</td>
                                                </tr>
                                            ))
                                        }
                                    </tbody>
                                </Table>
                            </FormGroup> : null
                        }
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

export default withRouter(SubscriptionForm);
