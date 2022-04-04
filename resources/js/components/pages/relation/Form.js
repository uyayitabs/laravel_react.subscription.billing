import React from "react";
import Select from "react-select";
import { withRouter } from "react-router-dom";
import ReactQuill from "react-quill";

import { Button, Modal, ModalBody, ModalFooter, ModalHeader, Label, FormGroup, Row, Col } from "reactstrap";
import { AvForm, AvGroup, AvInput, AvFeedback } from "availity-reactstrap-validation";

import { GetRelationsDependencies, AddRelation, UpdateRelation } from "../../controllers/relations";

const statusOpt = [
    {
        value: 1,
        label: "Active"
    },
    {
        value: 0,
        label: "Inactive"
    }
];

class RelationForm extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            relation: {},
            relation_types: [],
            status: null,
            relation_type: null,
            formType: null,
            formName: 'Relation'
        };

        this.toggle = this.toggle.bind(this)
        this.handleInputChange = this.handleInputChange.bind(this)
        this.handleQuillChange = this.handleQuillChange.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
    }

    toggle() {
        this.props.hide();
    }

    handleInputChange(e) {
        e.persist()
        
        let relation = Object.assign({}, this.state.relation)

        relation[e.target.name] = e.target.value

        this.setState({ relation })
    }

    handleSelectChange(name, value) {
        let relation = Object.assign({}, this.state.relation)

        relation[name] = value.value

        this.setState({ 
            relation,
            [name]: value
        })
    }

    handleQuillChange(val) {
        let relation = Object.assign({}, this.state.relation)

        relation.info = val

        this.setState({ relation })
    }

    handleSubmit(e) {
        e.persist();
        console.log(this.state.relation);

        (async () => {
            await AddRelation(this.state.relation)
                .then(res => {
                    this.props.history.push(
                        "/relations/" + res.data.data.id + "/details"
                    );
                })
                .catch(err => {
                    console.log(err.response.data);
                });
        })().catch(err => {
            console.log(err);
        });

        // if (this.props.id) {
        //     this.state.relation.persons = null;
        //     this.state.relation.type = null;

        //     this.state.relation._method = "PATCH";

        //     (async () => {
        //         await UpdateRelation(this.props.id, this.state.relation)
        //             .then(res => {
        //                 this.toggle();

        //                 this.props.action(res.data.data);
        //             })
        //             .catch(err => {
        //                 console.log(err.response.data);
        //             });
        //     })().catch(err => {
        //         console.log(err);
        //     });
        // } else {
        //     (async () => {
        //         await AddRelation(this.state.relation)
        //             .then(res => {
        //                 this.props.history.push(
        //                     "/relations/" + res.data.data.id + "/details"
        //                 );
        //             })
        //             .catch(err => {
        //                 console.log(err.response.data);
        //             });
        //     })().catch(err => {
        //         console.log(err);
        //     });
        // }
    }

    componentWillMount() {
        (async () => {
            await GetRelationsDependencies()
                .then(res => {
                    const datas = res.data,
                        relation_types = datas.relation_types;

                    let relationTypes = [];
                    
                    relation_types.forEach((relation_type, index) => {
                        relationTypes.push({
                            label: relation_type.type,
                            value: relation_type.id
                        });
                    });

                    this.setState({
                        relationTypesOpts: relationTypes,
                        formType: 'Add New'
                    });

                    if (this.state.relation && this.state.relation.type) {
                        this.setState({
                            relation_type_id: {
                                value: this.state.relation_type_id.type.id,
                                label: this.state.relation_type_id.type.type
                            }
                        });
                    }
                })
                .catch(err => {
                    console.log(err.response.data);
                });
        })().catch(err => {
            console.log(err);
        });
    }

    render() {
        return (
            <Modal isOpen={this.props.show} toggle={this.toggle} centered backdrop="static">
                <AvForm onSubmit={this.handleSubmit}>
                    <ModalHeader>{this.state.formType} {this.state.formName}</ModalHeader>
                    <ModalBody className="mt-3 mb-3">
                        <Row>
                            <Col md={2}>Relations:</Col>
                            <Col md={10}>
                                <FormGroup className="row">
                                    <Col md={6}>
                                        <Select
                                            id="status"
                                            className="react-select-container"
                                            classNamePrefix="react-select"
                                            placeholder="Status"
                                            options={statusOpt}
                                            value={this.state.status}
                                            onChange={ this.handleSelectChange.bind(this, 'status') }
                                            maxMenuHeight="100"
                                        />
                                    </Col>
                                    <Col md={6}>
                                        <Select
                                            id="relation_type_id"
                                            className="react-select-container"
                                            classNamePrefix="react-select"
                                            placeholder="Type"
                                            options={this.state.relationTypesOpts}
                                            value={this.state.relation_type_id}
                                            onChange={ this.handleSelectChange.bind(this, 'relation_type_id') }
                                            maxMenuHeight="100"
                                        />
                                    </Col>
                                </FormGroup>
                                <FormGroup className="row">
                                    <Col md={6}>
                                        <AvInput
                                            id="company_name"
                                            name="company_name"
                                            placeholder='Company name'
                                            value={this.state.relation.company_name || null}
                                            onChange={this.handleInputChange}
                                        />
                                    </Col>
                                    <Col md={6}>
                                        <AvInput
                                            id="email"
                                            name="email"
                                            placeholder= 'Email'
                                            value={this.state.relation.email || null}
                                            onChange={this.handleInputChange}
                                        />
                                    </Col>
                                </FormGroup>
                                <FormGroup className="row">
                                    <Col md={6}>
                                        <AvInput
                                            id="credit_limit"
                                            name="credit_limit"
                                            placeholder= 'Credit Limit'
                                            value={this.state.relation.credit_limit || null}
                                            onChange={this.handleInputChange}
                                        />
                                    </Col>
                                    <Col md={6}>
                                        <AvInput
                                            id="bank_account"
                                            name="bank_account"
                                            placeholder='Bank account'
                                            value={this.state.relation.bank_account || null}
                                            onChange={this.handleInputChange}
                                        />
                                    </Col>
                                </FormGroup>
                                <FormGroup className="row">
                                    <Col md={6}>
                                        <AvInput
                                            id="kvk"
                                            name="kvk"
                                            placeholder='KVK'
                                            value={this.state.relation.kvk || null}
                                            onChange={this.handleInputChange}
                                        />
                                    </Col>
                                    <Col md={6}>
                                        <AvInput
                                            id="fax"
                                            name="fax"
                                            placeholder="Fax"
                                            value={this.state.relation.fax || null}
                                            onChange={this.handleInputChange}
                                        />
                                    </Col>
                                </FormGroup>
                                <FormGroup className="row">
                                    <Col md={6}>
                                        <AvInput
                                            id="website"
                                            name="website"
                                            placeholder="Website"
                                            value={this.state.relation.website || null}
                                            onChange={this.handleInputChange}
                                        />
                                    </Col>
                                    <Col md={3}>
                                        <AvInput
                                            id="iban"
                                            name="iban"
                                            placeholder='Iban'
                                            value={this.state.relation.iban || null}
                                            onChange={this.handleInputChange}
                                        />
                                    </Col>
                                    <Col md={3}>
                                        <AvInput
                                            id="bic"
                                            name="bic"
                                            placeholder="BIC"
                                            value={this.state.relation.bic || null}
                                            onChange={this.handleInputChange}
                                        />
                                    </Col>
                                </FormGroup>
                                <FormGroup className="row">
                                    <Col>
                                        <AvInput
                                            id="payment_conditions"
                                            name="payment_conditions"
                                            placeholder= 'Payment conditions'
                                            value={
                                                this.state.relation.payment_conditions ||
                                                null
                                            }
                                            onChange={this.handleInputChange}
                                        />
                                    </Col>
                                    <Col md={3}>
                                        <AvInput
                                            id="phone"
                                            name="phone"
                                            placeholder="Phone"
                                            value={this.state.relation.phone || null}
                                            onChange={this.handleInputChange}
                                        />
                                    </Col>
                                    <Col md={3}>
                                        <AvInput
                                            id="vat_no"
                                            name="vat_no"
                                            placeholder='Vat No'
                                            value={this.state.relation.vat_no || null}
                                            onChange={this.handleInputChange}
                                        />
                                    </Col>
                                </FormGroup>
                                <AvGroup className="row">
                                    <Col>
                                        <ReactQuill
                                            placeholder='Info...'
                                            value={this.state.relation.info || null}
                                            onChange={this.handleQuillChange}
                                        />
                                    </Col>
                                </AvGroup>
                            </Col>
                        </Row>
                    </ModalBody>
                    <ModalFooter className="justify-content-between">
                        <span className="btn btn-danger" onClick={this.toggle}>
                            Cancel
                        </span>
                        <Button color="primary"> Submit </Button>
                    </ModalFooter>
                </AvForm>
            </Modal>
        );
    }
}

export default withRouter(RelationForm);
