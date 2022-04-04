import React from "react";
import Select from "react-select";

import { Button, Modal, ModalBody, ModalFooter, ModalHeader, Label, FormGroup, Col, Row } from "reactstrap";
import { AvForm, AvInput, AvGroup } from "availity-reactstrap-validation";

import { GetPersonDependencies, AddPerson, UpdatePerson } from "../../../controllers/relations";

const statusOpts = [
    { value: "1", label: "Pending" },
    { value: "2", label: "New" },
    { value: "3", label: "Active" },
    { value: "4", label: "Suspended" },
    { value: "5", label: "Deleted" }
];

const genderOpts = [
    { value: "Male", label: "Male" },
    { value: "Female", label: "Female" }
];

const titleOpts = [
    { value: "Ms", label: "Ms" },
    { value: "Mr", label: "Mr" },
    { value: "Mrs", label: "Mrs" },
    { value: "Dr", label: "Dr" },
    { value: "Atty", label: "Atty" },
    { value: "Engr", label: "Engr" }
];

class PersonForm extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            formType: null,
            formName: 'Person',
            relation_id: this.props.id,
            person: this.props.selectedData ? this.props.selectedData : {},
            status: null,
            person_type: null,
            title: null,
            gender: null
        };

        this.toggle = this.toggle.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    toggle() {
        this.props.hide();
    }

    handleSelectChange = (name, value) => {
        let change = {};
        change[name] = value;
        this.setState(change);
    };

    handleInputChange(e, type) {
        if (type) {
            if (!this.state[type]) {
                this.setState({ [type]: [] })
            }
            if (this.state[type] && !this.state[type][e.target.name]) {
                this.setState({
                    [type]: {
                        ...this.state[type],
                        [e.target.name]: ''
                    }
                })                    
            }

            let thisType = Object.assign({}, this.state[type])
            thisType[e.target.name] = e.target.value
            this.setState({ [type]: thisType })
        } else {
            this.state.person[e.target.name] = e.target.value;
        }
        e.persist();
    }

    handleSubmit(e) {
        e.persist();

        let person = Object.assign({}, this.state.person)

        if (this.state.status && this.state.status.value) {
            (person.status = this.state.status.value)
        }
        if (this.state.person_type) {
            (person.person_type_id = this.state.person_type.value)
        }
        if (this.state.title && this.state.title.value) {
            (person.title = this.state.title.value)
        }
        if (this.state.gender && this.state.gender.value) {
            (person.gender = this.state.gender.value)
        }

        this.setState({ person })

        if (this.state.person.id) {
            this.state.person.person_type = null;
            this.state.person._method = "PATCH";

            (async () => {
                await UpdatePerson(this.props.selectedData.id, this.state.person)
                    .then(res => {
                        this.toggle();

                        this.props.update(this.props.selectedDataRow);
                    })
                    .catch(err => {
                        console.log(err);
                    });
            })().catch(err => {
                console.log(err);
            });
        } else {
            let person = Object.assign({}, this.state.person)
            person.relation_id = this.state.relation_id;
            person.user = this.state.user;

            (async () => {
                await AddPerson(person)
                    .then(res => {
                        this.toggle();

                        this.props.update(res.data.data, null);
                    })
                    .catch(err => {
                        console.log(err);
                    });
            })().catch(err => {
                console.log(err);
            });
        }
    }

    componentDidMount() {
        (async () => {
            await GetPersonDependencies()
                .then(res => {
                    const datas = res.data,
                        person_types = datas.person_types.data;

                    let personType = [];
                    person_types.forEach(person_type => {
                        personType.push({
                            label: person_type.type,
                            value: person_type.id
                        });
                    });

                    this.setState({ 
                        personTypeOpts: personType, 
                        user: this.props.selectedData && this.props.selectedData.user ? this.props.selectedData.user : null,
                        password: this.props.selectedData && this.props.selectedData.user ? this.props.selectedData.user : null,
                        formType: 'Edit' });

                    if (this.props.selectedData) {
                        if (this.state.person.person_type_id) {
                            const person_type = personType.find(
                                item =>
                                    item.value ===
                                    this.state.person.person_type_id
                            );
                            this.setState({ person_type });
                        }

                        if (this.state.person.status) {
                            const status = statusOpts.find(
                                item => item.value === this.state.person.status
                            );
                            this.setState({ status });
                        }

                        if (this.state.person.title) {
                            const title = titleOpts.find(
                                item =>
                                    item.value.toLowerCase() ===
                                    this.state.person.title.toLowerCase()
                            );
                            this.setState({ title });
                        }

                        if (this.state.person.gender) {
                            const gender = genderOpts.find(
                                item =>
                                    item.value.toLowerCase() ===
                                    this.state.person.gender.toLowerCase()
                            );
                            this.setState({ gender });
                        }
                    } else {
                        this.setState({
                            formType: 'Add New'
                        })
                    } 
                })
                .catch(err => {
                    console.log(err);
                });
        })().catch(err => {
            console.log(err);
        });
    }

    render() {
        return (
            <Modal isOpen={this.props.show} toggle={this.toggle} centered>
                <AvForm onSubmit={this.handleSubmit}>
                    <ModalHeader>{this.state.formType} {this.state.formName}</ModalHeader>
                    <ModalBody className="mt-3 mb-3">
                    <Row>
                        <Col md={2}>{this.state.formName}:</Col>
                        <Col md={10}>
                            <FormGroup className="row">
                                <Col>
                                    <Select
                                        id="status"
                                        className="react-select-container"
                                        classNamePrefix="react-select"
                                        placeholder="Status"
                                        options={statusOpts}
                                        value={this.state.status}
                                        onChange={this.handleSelectChange.bind(
                                            this,
                                            "status"
                                        )}
                                        maxMenuHeight="100"
                                        required
                                    />
                                </Col>
                                <Col>
                                    <Select
                                        id="person_type"
                                        className="react-select-container"
                                        classNamePrefix="react-select"
                                        placeholder="Type"
                                        options={this.state.personTypeOpts}
                                        value={this.state.person_type}
                                        onChange={this.handleSelectChange.bind(
                                            this,
                                            "person_type"
                                        )}
                                        maxMenuHeight="100"
                                        required
                                    />
                                </Col>
                            </FormGroup>
                            <FormGroup className="row">
                                <Col>
                                    <Select
                                        id="title"
                                        className="react-select-container"
                                        classNamePrefix="react-select"
                                        placeholder="Title"
                                        options={titleOpts}
                                        value={this.state.title}
                                        onChange={this.handleSelectChange.bind(
                                            this,
                                            "title"
                                        )}
                                        maxMenuHeight="100"
                                        required
                                    />
                                </Col>
                                <Col>
                                    <Select
                                        id="gender"
                                        className="react-select-container"
                                        classNamePrefix="react-select"
                                        placeholder="Gender"
                                        options={genderOpts}
                                        value={this.state.gender}
                                        onChange={this.handleSelectChange.bind(
                                            this,
                                            "gender"
                                        )}
                                        maxMenuHeight="100"
                                        required
                                    />
                                </Col>
                            </FormGroup>
                            <FormGroup className="row">
                                <Col xs={5} className="pr-0">
                                    <AvInput
                                        id="first_name"
                                        name="first_name"
                                        placeholder="Firstname"
                                        value={this.state.person.first_name || null}
                                        onChange={(e) => this.handleInputChange(e)}
                                        required
                                    />
                                </Col>
                                <Col xs={2}>
                                    <AvInput
                                        id="middle_name"
                                        name="middle_name"
                                        placeholder="Middlename"
                                        value={this.state.person.middle_name || null}
                                        onChange={(e) => this.handleInputChange(e)}
                                        required
                                    />
                                </Col>
                                <Col xs={5} className="pl-0">
                                    <AvInput
                                        id="last_name"
                                        name="last_name"
                                        placeholder="Lastname"
                                        value={this.state.person.last_name || null}
                                        onChange={(e) => this.handleInputChange(e)}
                                        required
                                    />
                                </Col>
                            </FormGroup>
                            <FormGroup className="row">
                                <Col>  
                                    <AvInput
                                        id="email"
                                        name="email"
                                        placeholder="Email"
                                        value={this.state.person.email || null}
                                        onChange={(e) => this.handleInputChange(e)}
                                        required
                                    />
                                </Col>
                                <Col>
                                    <AvInput
                                        id="language"
                                        name="language"
                                        placeholder="Language"
                                        value={this.state.person.language || null}
                                        onChange={(e) => this.handleInputChange(e)}
                                    />
                                </Col>
                            </FormGroup>
                            <FormGroup className="row">
                                <Col>
                                    <AvInput
                                        id="phone"
                                        name="phone"
                                        placeholder="Phone"
                                        value={this.state.person.phone || null}
                                        onChange={(e) => this.handleInputChange(e)}
                                    />
                                </Col>
                                <Col>
                                    <AvInput
                                        id="mobile"
                                        name="mobile"
                                        placeholder="Mobile"
                                        value={this.state.person.mobile || null}
                                        onChange={(e) => this.handleInputChange(e)}
                                    />
                                </Col>
                            </FormGroup>
                            <FormGroup className="row">
                                <Col>
                                    <AvInput
                                        id="linkedin"
                                        name="linkedin"
                                        placeholder="Linkedin"
                                        value={this.state.person.linkedin || null}
                                        onChange={(e) => this.handleInputChange(e)}
                                    />
                                </Col>
                                <Col>
                                    <AvInput
                                        id="facebook"
                                        name="facebook"
                                        placeholder="Facebook"
                                        value={this.state.person.facebook || null}
                                        onChange={(e) => this.handleInputChange(e)}
                                    />   
                                </Col>
                            </FormGroup>
                        </Col>
                    </Row>
                    <Row className="mt-3 pt-3"  style={{ borderTop: '1px solid #00000033' }}>
                        <Col md={2}>User:</Col>
                        <Col md={10}>
                            <AvGroup className="row">
                                <Col>
                                    <AvInput
                                        id="username"
                                        name="username"
                                        placeholder="Username"
                                        value={ this.state.user && this.state.user.username ? this.state.user.username : '' }
                                        onChange={(e) => this.handleInputChange(e, 'user')}
                                        disabled={ this.props.selectedData && this.props.selectedData.user ? true : false }
                                    />
                                </Col>
                                <Col>
                                    <AvInput
                                        id="password"
                                        type="password"
                                        name="password"
                                        placeholder="Password"
                                        value={ this.state.user && this.state.user.password ? this.state.user.password : '' }
                                        onChange={(e) => this.handleInputChange(e, 'password')}
                                        disabled={ this.props.selectedData && this.props.selectedData.user ? true : false }
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
                        <Button color="primary">Submit</Button>
                    </ModalFooter>
                </AvForm>
                
            </Modal>
        );
    }
}

export default PersonForm;
