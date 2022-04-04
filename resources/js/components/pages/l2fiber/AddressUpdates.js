import React from "react";

import { CardHeader, CardBody, Button, Form, FormGroup, Label, Input } from "reactstrap";

import DataTable from '../../components/DataTable';
import { Addresses } from '../../controllers/l2fiber';
import Loader from '../../components/Loader';

class AddressUpdates extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            changedSince: '',
            data: {
                changedSince: '',
                offset: '',
                limit: ''
            },
            loading: false
        };

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange(e) {
        this.setState({ 
            data: {
                ...this.state.data,        
                [e.target.name]: e.target.value
              }            
         });
    }

    handleSubmit(e) {
        let self = this;
        self.setState({ 
            loading: true
        });
        e.preventDefault();
        (async () => {
            await Addresses(this.state.data)
                .then(res => {
                    // Dirty fix add "id" attribute
                    res.data.map((item,key)=> {
                        res.data[key]['id'] = key;
                    });
                    setTimeout(() => {
                        self.setState({ 
                            addresses: res.data,
                            loading: false
                        });
                    }, 500);
                })
                .catch(err => {
                    console.log(err.response)
                });
        })().catch(err => {
            console.log(err.response)
        })
    }

    render() {
        const Addresses = (props) => {
            return (
                <CardBody>
                    { !this.state.loading ? 
                    <DataTable
                        table="Addresses"
                        data={props.addresses !== undefined ? props.addresses : []}
                        columns={[
                        {
                            dataField: "publicId",
                            text: "Public Id",
                            sort: true
                        },
                        {
                            dataField: "number",
                            text: "Number",
                            sort: true
                        },
                        {
                            dataField: "room",
                            text: "Room",
                            sort: true
                        },
                        {
                            dataField: "street",
                            text: "Street",
                            sort: true
                        },
                        {
                            dataField: "postalCode",
                            text: "Postal Code",
                            sort: true
                        },
                        {
                            dataField: "addressStatus",
                            text: "Status",
                            sort: true
                        }
                        ]}
                        search={true}
                        pagination={true}
                    />  :  <Loader /> }
                </CardBody>
            )
        }

        return (
            <React.Fragment>
                <CardHeader>
                    <h4 className="mb-0">Address Updates</h4>
                </CardHeader>
                <CardBody>
                    <Form onSubmit={this.handleSubmit}>
                        <FormGroup>
                            <Label>Changed Since</Label>
                            <Input
                                bsSize="lg"
                                type="date"
                                name="changedSince"
                                value={this.state.data.changedSince}
                                onChange={this.handleChange}
                            />
                        </FormGroup>
                        <FormGroup>
                            <Label>Offset</Label>
                            <Input
                                bsSize="lg"
                                type="number"
                                name="offset"
                                value={this.state.data.offset}
                                onChange={this.handleChange}
                            />
                        </FormGroup>
                        <FormGroup>
                            <Label>Limit</Label>
                            <Input
                                bsSize="lg"
                                type="number"
                                name="limit"
                                value={this.state.data.limit}
                                onChange={this.handleChange}
                            />
                        </FormGroup>
                        <FormGroup>
                            <Button color="primary">Submit</Button>
                        </FormGroup>
                    </Form>
                </CardBody>

                <Addresses addresses={this.state.addresses}/>
            </React.Fragment>
        );
    }
}

export default AddressUpdates;
