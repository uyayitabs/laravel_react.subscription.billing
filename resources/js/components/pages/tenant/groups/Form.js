import React from "react";
import Select from "react-select";

import { Button, Modal, ModalBody, ModalFooter, ModalHeader, Row, Col, CustomInput } from "reactstrap";
import { AvForm, AvInput, AvGroup } from "availity-reactstrap-validation";

import { AddGroup, UpdateGroup} from '../../../controllers/tenants';

const options = [
    { value: 'role1', label: 'Role 1' },
    { value: 'role2', label: 'Role 2' },
    { value: 'role3', label: 'Role 3' },
    { value: 'role4', label: 'Role 4' },
    { value: 'role5', label: 'Role 5' },
    { value: 'role6', label: 'Role 6' },
    { value: 'role7', label: 'Role 7' },
    { value: 'role8', label: 'Role 8' },
    { value: 'role9', label: 'Role 9' },
    { value: 'role10', label: 'Role 10' },
    { value: 'role11', label: 'Role 11' },
    { value: 'role12', label: 'Role 12' },
];

class GroupForm extends React.Component {
    constructor(props) {
        super(props)    

        let type;

        if (this.props.selectedData) {
            type = 'Edit';
        } else {
            type = 'Add';
        }

        this.state = {
            formType: type,
            formName: 'Group',
            id: null,
            name: this.props.selectedData,
            selectedOption: null,
        }

        this.toggle = this.toggle.bind(this);
        this.handleInputChange = this.handleInputChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange = selectedOption => {
        this.setState(
            { selectedOption },
            () => console.log(`Option selected:`, this.state.selectedOption)
        );
    };
    
    toggle() {
        this.props.hide()
    }

    update(val) {
        this.props.update(val)
    }

    handleInputChange(e) {
        this.setState({ [e.target.name]: e.target.value });
        e.persist();
    }

    handleSubmit(e) {
        e.preventDefault();

        const group = {
            name: this.state.name,
            role: this.state.selectedOption
        };
        

        if (this.props.selectedData) {
            group._method = 'PATCH';
            (async () => {
                
                await UpdateGroup(this.state.id, group)
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
                await AddGroup(group)
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
        
    render() {
        return (
            <Modal
                className="form-subscription-lines"
                isOpen={this.props.show}
                toggle={this.toggle}
                centered
            >
                <AvForm onSubmit={this.handleSubmit}>
                    <ModalHeader>{ this.state.formType} { this.state.formName }</ModalHeader>
                    <ModalBody className="mt-3 mb-3">
                        <AvGroup>
                            <AvInput 
                                id="name"
                                name="name" 
                                value= { this.state.name }
                                placeholder="Group Name" 
                                onChange={ this.handleInputChange } 
                            />
                        </AvGroup>
                        <AvGroup>
                            <Select
                                id="role"
                                name="role"
                                placeholder="Select Role"
                                value={ this.state.selectedOption }
                                onChange={this.handleChange}
                                options={options}
                                isMulti
                            />
                        </AvGroup>
                    </ModalBody>
                    <ModalFooter className="justify-content-between">
                        <span className="btn btn-danger" onClick={this.toggle}>Cancel</span>
                        <Button color="primary">Submit</Button>
                    </ModalFooter>
                </AvForm>
            </Modal >
        );
    }
}

export default (GroupForm);