import React from "react";

import { GetRelation, UpdateRelation, GetRelationsDependencies } from "../../controllers/relations";
import SubscriptionForm from "../subscription/Form";
import Persons from "./persons/List";
import Addresses from "./addresses/List";
import Subscriptions from "./subscriptions/List";
import Invoices from "./invoices/List";

import DetailsPage from "../../layouts/DetailsPage";
import Details from "../../components/Details";
import Loader from "../../components/Loader";

class RelationDetails extends React.Component {
    details = null

    dropdownItems = [{
        label: 'New Subscription',
        function: 'toggleForm'
    }]

    constructor(props) {
        super(props)

        this.toggleModal = this.toggleModal.bind(this)
        this.handlerSubscription = this.handlerSubscription.bind(this)

        this.state = {
            relation: null,
            relationTypesOpts: null,
            tabs: null,
            isOpen: false,
            loading: true
        }
    }

    toggleModal() {
        this.setState({ isOpen: !this.state.isOpen })
    }

    handlerSubscription(subscription) {
        this.setState({ subscription })
    }

    componentDidMount() {
        (async () => {
            await GetRelationsDependencies()
                .then(res => {
                    const datas = res.data,
                        relation_types = datas.relation_types

                    let relationTypes = []
                    
                    relation_types.forEach((relation_type, index) => {
                        relationTypes.push({
                            label: relation_type.type,
                            value: relation_type.id
                        })
                    })

                    this.setState({ relationTypesOpts: relationTypes })
                    
                    this.details = [{
                        label: 'Company',
                        data: 'company_name',
                        type: 'text'
                    }, {
                        label: 'KVK',
                        data: 'kvk',
                        type: 'text'
                    }, {
                        label: 'VAT no.',
                        data: 'vat_no',
                        type: 'text'
                    }, {
                        label: 'Phone',
                        data: 'phone',
                        type: 'text'
                    }, {
                        label: 'Fax',
                        data: 'fax',
                        type: 'text'
                    }, {
                        label: 'Website',
                        data: 'website',
                        type: 'text'
                    }, {
                        label: 'Customer no.',
                        data: 'customer_number',
                        type: 'text',
                        disabled: true
                    }, {
                        label: 'Type',
                        data: 'relation_type_id',
                        type: 'select',
                        opts: relationTypes
                    }, {
                        label: 'Status',
                        data: 'status',
                        type: 'select',
                        opts: [{
                            value: 1,
                            label: "ACTIVE"
                        }, {
                            value: 0,
                            label: "INACTIVE"
                        }]
                    }, {
                        label: 'Credit Limit',
                        data: 'credit_limit',
                        type: 'text'
                    }, {
                        label: 'Payment conditions',
                        data: 'payment_conditions',
                        type: 'text'
                    }, {
                        label: 'IBAN',
                        data: 'iban',
                        type: 'text'
                    }, {
                        label: 'BIC',
                        data: 'bic',
                        type: 'text'
                    }]

                    this.setState({
                        tabs: [{
                            title: 'Details',
                            component: <Details 
                                title='customer_number'
                                getApi={ GetRelation } 
                                updateApi={ UpdateRelation }
                                id={ this.props.match.params.id } 
                                include='tenant' 
                                details={ this.details } 
                                quill="info"
                                toggleForm={ this.toggleModal }
                                dropdownItems={ this.dropdownItems }  />
                        }, {
                            title: 'Addresses',
                            component: <Addresses id={ this.props.match.params.id } />
                        }, {
                            title: 'Persons',
                            component: <Persons id={ this.props.match.params.id } />
                        }, {
                            title: 'Subscriptions',
                            component: <Subscriptions id={ this.props.match.params.id } />
                        }, {
                            title: 'Invoices',
                            component: <Invoices id={ this.props.match.params.id } />
                        }], 
                        loading: false
                    })
                })
                .catch(err => {
                    console.log(err.response.data)
                });
        })().catch(err => {
            console.log(err)
        })
    }

    render() {
        const { loading, relationTypesOpts, tabs } = this.state

        return (
            <React.Fragment>
                { !loading && relationTypesOpts && tabs ?
                    <DetailsPage 
                        name="Relation"
                        tabs={ tabs }
                    /> : <Loader />
                }
                
                { this.state.isOpen ?
                    <SubscriptionForm
                        show={ this.state.isOpen }
                        hide={ this.toggleModal }
                        update={ this.handlerSubscription }
                        relation={ this.state.relation }
                    /> : null
                }
            </React.Fragment>
        )
    }
}

export default RelationDetails;