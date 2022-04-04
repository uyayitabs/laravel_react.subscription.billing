import React from "react";
import Select from "react-select";

import { CardHeader, CardBody, Button, Form, FormGroup, Label, Input, CustomInput } from "reactstrap";
import { M7Call } from '../../controllers/m7';

const LineTypeOpts = [
    {
        value: 'ADSL',
        label: 'ADSL'
    }, {
        value: 'VDSL',
        label: 'VDSL'
    }, {
        value: 'FTTH',
        label: 'FTTH'
    }, {
        value: 'Unknown',
        label: 'Unknown'
    },
]

const LineProfileOpts = [
    {
        value: '20',
        label: '20mb'
    }, {
        value: '50',
        label: '50mb'
    }, {
        value: '100',
        label: '100mb'
    }
]

const ChannelListTypes = [
    {
        value: 'CDS-R_HD',
        label: 'CDS-R_HD'
    }, {
        value: 'CDS-R_HD+',
        label: 'CDS-R_HD+'
    }, {
        value: 'CDS-R_OTT',
        label: 'CDS-R_OTT'
    }, {
        value: 'CDS-R_SD',
        label: 'CDS-R_SD'
    }
]

class SetLineProperties extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            data: {
                LineType: '',
                LineProfile: '',
                LineMinDownload: '',
                KpnPackageID: '',
                ChannelListType: '',
                CustomerNumber: ''
            },
            LineType: null,
            LineProfile: null,
            ChannelListType: null
        };

        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange(parent, e) {
        if (parent) {
            this.setState({
                data: {
                    ...this.state.data,
                    [parent]: {
                        ...this.state.data[parent],
                        [e.target.name]: e.target.value
                    }
                }
            });
        } else {
            this.setState({
                data: {
                    ...this.state.data,
                    [e.target.name]: e.target.value
                }
            });
        }
    }

    handleSelectChange = async (parent, name, value) => {
        await this.setState({
            data: {
                ...this.state.data,
                [name]: value.value
            }
        });

        this.setState({
            ...this.state,
            [name]: value
        });
    }

    async handleSubmit(e) {
        e.preventDefault();
        this.props.loading();

        M7Call('SetLineProperties', this.state.data).then((res) => {
            let { data } = res;
            this.props.update(data);
        });
    }

    render() {
        return (
            <React.Fragment>
                <CardHeader>
                    <h4 className="mb-0">Set Line Properties</h4>
                </CardHeader>
                <CardBody>
                    <Form className="form-m7" onSubmit={this.handleSubmit}>
                        <FormGroup className="row">
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Customer Number</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="CustomerNumber"
                                    value={this.state.data.CustomerNumber}
                                    onChange={(e) => this.handleChange(null, e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Line Type</Label>
                                <Select
                                    className="react-select-container"
                                    classNamePrefix="react-select"
                                    options={LineTypeOpts}
                                    value={this.state.LineType}
                                    onChange={(e) => this.handleSelectChange(null, 'LineType', e)}
                                    maxMenuHeight="100"
                                    name="LineType"
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Line Profile</Label>
                                <Select
                                    className="react-select-container"
                                    classNamePrefix="react-select"
                                    options={LineProfileOpts}
                                    value={this.state.LineProfile}
                                    onChange={(e) => this.handleSelectChange(null, 'LineProfile', e)}
                                    maxMenuHeight="100"
                                    name="LineProfile"
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Line Min Download</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="LineMinDownload"
                                    value={this.state.data.LineMinDownload}
                                    onChange={(e) => this.handleChange(null, e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>KPN Package ID</Label>
                                <Input
                                    bsSize="lg"
                                    type="text"
                                    name="KpnPackageID"
                                    value={this.state.data.KpnPackageID}
                                    onChange={(e) => this.handleChange(null, e)}
                                />
                            </div>
                            <div className="col-12 col-md-3 mb-2">
                                <Label>Channel List Type</Label>
                                <Select
                                    className="react-select-container"
                                    classNamePrefix="react-select"
                                    options={ChannelListTypes}
                                    value={this.state.ChannelListType}
                                    onChange={(e) => this.handleSelectChange(null, 'ChannelListType', e)}
                                    maxMenuHeight="100"
                                    name="ChannelListType"
                                />
                            </div>
                        </FormGroup>

                        <FormGroup>
                            <Button color="primary">Submit</Button>
                        </FormGroup>
                    </Form>
                </CardBody>
            </React.Fragment>
        );
    }
}

export default SetLineProperties;
