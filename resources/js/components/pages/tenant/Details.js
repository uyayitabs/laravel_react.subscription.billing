import React from "react";

import { GetTenantLists, GetTenant, UpdateTenant } from "../../controllers/tenants";

import DetailsPage from "../../layouts/DetailsPage";
import Details from "../../components/Details";
import Loader from "../../components/Loader";
import NumberRanges from './number_ranges/List';
import Groups from './groups/List';

class TenantDetails extends React.Component {
    constructor(props) {
        super(props)

        this.state = {
            tenant: {},
            relations: [],
            loading: true
        }

        this.fullnameFormatter = (cell, row, rowIndex, formatExtraData) => {
            return row.persons != null ? row.persons[0].first_name + " " + row.persons[0].last_name : "-"
        }
    }

    componentDidMount() {
        GetTenantLists()
            .then(res => {
                const tenants = res.data.data;
                
                let tenantOpts = []

                tenants.forEach((tenant) => {
                    if (parseInt(this.props.match.params.id) !== parseInt(tenant.id)) {                        
                        tenantOpts.push({
                            label: tenant.name,
                            value: tenant.id
                        })
                    }
                })

                this.setState({ tenantOpts })

                this.details = [{
                    label: 'Name',
                    data: 'name',
                    type: 'text'
                }, {
                    label: 'Parent Tenant',
                    data: 'parent_id',
                    type: 'select',
                    opts: tenantOpts
                }, {
                    label: 'Billing Day',
                    data: 'billing_day',
                    type: 'text'
                }]

                this.setState({
                    tabs: [
                        {
                            title: 'Details',
                            component: <Details 
                                getApi={ GetTenant }  
                                updateApi={ UpdateTenant }
                                id={ this.props.match.params.id } 
                                include='parent'
                                details={ this.details } />
                        },
                        {
                            title: 'Number Ranges',
                            component: <NumberRanges id={ this.props.match.params.id } />
                        },
                        {
                            title: 'Groups',
                            component: <Groups id={ this.props.match.params.id } />
                        }
                    ],
                    loading: false
                })
            })
            .catch(err => {
                console.log(err)
            })
    }

    render() {
        const { loading, tenantOpts, tabs } = this.state

        return (
            <React.Fragment>
                { !loading && tenantOpts && tabs ?
                    <DetailsPage 
                        name="Tenant"
                        tabs={ tabs }
                    /> : <Loader />
                }
            </React.Fragment>
        );
    }
}

export default TenantDetails;