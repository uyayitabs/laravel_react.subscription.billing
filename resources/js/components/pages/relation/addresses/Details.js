import React from "react";

import { GetAddress, UpdateAddress } from '../../../controllers/relations';

import DetailsPage from "../../../layouts/DetailsPage";
import Loader from '../../../components/Loader';
import Details from "../../../components/Details";

class AddressDetails extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            address: null,
            address_id: this.props.match.params.id,
            tabs: null,
            loading: true,
            details: [{
                label: 'Address Type',
                data: 'address_type.type'
            }, {
                label: 'Street1',
                data: 'street1'
            }, { 
                label: 'Street2',
                data: 'street2'
            }, {
                label: 'House number',
                data: 'house_number'
            }, {
                label: 'Extension',
                data: 'house_number_suffix'
            }, {
                label: 'Room',
                data: 'room'
            }, {
                label: 'Zipcode',
                data: 'zipcode'
            }, {
                label: 'City',
                data: 'city'
            }, {
                label: 'Country',
                data: 'country.name'
            }],
            dropdownItems: [{
                label: 'Edit',
                function: 'toggleEdit'
            }]
        }

        this.handler = this.handler.bind(this)
    }

    handler(address) {
        const tabs = Object.assign({}, this.state);
        tabs[0].component = <Details data={ address } />
        this.setState({ 
            address,
            tabs
        })
    }

    componentDidMount() {
        (async () => {
            await GetAddress(null, this.state.address_id)
                .then(res => {
                    const address = res.data.data;
                    this.setState({
                        address,
                        tabs: [
                            {
                                title: 'Details',
                                component: <Details data={ address } details={ this.state.details } dropdownItems={ this.state.dropdownItems } api={ UpdateAddress } />
                            }
                        ]
                    })

                    this.setState({ loading: false })
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
        const { loading, tabs } = this.state

        return (
            <React.Fragment>
                { !loading ?
                    <DetailsPage 
                        name="Address"
                        tabs={ tabs }
                    /> : <Loader />
                }
            </React.Fragment>
        )
    }
}

export default AddressDetails;