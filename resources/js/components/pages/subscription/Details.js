import React from "react";

import { GetSubscription, UpdateSubscription } from "../../controllers/subscriptions";
import { GenerateInvoice } from "../../controllers/invoices";

import DetailsPage from "../../layouts/DetailsPage";
import Details from "../../components/Details";
import SubscriptionLines from './subscription_lines/List';

class SubscriptionDetail extends React.Component {
    constructor(props) {
        super(props)
        
        this.state = {
            details : [{
                label: 'Description',
                data: 'description',
                type: 'text'
            }, {
                label: 'Customer #',
                data: 'relation.customer_number',
                type: 'text',
                disabled: true
            }, {
                label: 'Subscription Start',
                data: 'subscription_start',
                type: 'datepicker'
            }, {
                label: 'Subscription Stop',
                data: 'subscription_stop',
                type: 'datepicker'
            }, {
                label: 'Plan',
                data: 'plan.description_long',
                type: 'text',
                disabled: true
            }]
        }

        this.state = {
            subscription: {},
            subscriptionLines: [],
            tabs: [{
                title: 'Details',
                component: <Details 
                    getApi={ GetSubscription }  
                    updateApi={ UpdateSubscription }
                    id={ this.props.match.params.id } 
                    details={ this.state.details }  
                    quill="description_long" />
            }, {
                title: 'Subscription Lines',
                component: <SubscriptionLines id={ this.props.match.params.id } />
            }]
        }

        this.generateInvoice = this.generateInvoice.bind(this)
    }

    generateInvoice(e) {
        (async () => {
            await GenerateInvoice(this.state.subscription.sales_invoice.id)
                .then(res => {
                    const url = window.URL.createObjectURL(new Blob([res.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'invoice.pdf');
                    document.body.appendChild(link);
                    link.click();
                })
                .catch(err => {
                    console.log(err)
                });
        })()
            .catch(err => {
                console.log(err)
            })
        e.preventDefault();
    }

    // componentDidMount () {
    //     (async () => {
    //         await GetPlanSubscriptionLineTypes()
    //             .then(res => {
    //                 const plan_line_types = res.data.data;

    //                 let planLineTypesOpts = []
            
    //                 plan_line_types.forEach((plan_line_type, ) => {
    //                     planLineTypesOpts.push({
    //                         label: plan_line_type.line_type,
    //                         value: plan_line_type.id
    //                     })
    //                 })
    //             .catch(err => {
    //                 console.log(err)
    //             });
    //     })()
    //         .catch(err => {
    //             console.log(err)
    //         })
    // }

    render() {
        const { tabs } = this.state

        return (
            <React.Fragment>
                { tabs ?
                    <DetailsPage 
                        name="Subscription"
                        tabs={ tabs }
                    /> : null
                }
            </React.Fragment>
        );
    }
}

export default SubscriptionDetail;