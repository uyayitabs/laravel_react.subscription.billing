import React from "react";

import { Card, CardBody, Media } from "reactstrap";
import { CreditCard } from "react-feather";

import { GetSubscriptionsCount } from '../../../controllers/dashboard';

class SubscriptionsCountPanel extends React.Component {
    isMounted = false;

    constructor() {
        super();

        this.state = {
            subscriptions: '0',
            currentTenant: sessionStorage.getItem('tenant_id') ? sessionStorage.getItem('tenant_id') : null
        }

        this.checkTenantChange = this.checkTenantChange.bind(this)
        this.updateData = this.updateData.bind(this)     
    }

    getData() {
        (async () => {
            await GetSubscriptionsCount()
                .then(res => {
                    const subscriptions = res.data.data;

                    this.setState({ subscriptions });
                })
                .catch(err => {
                    console.log(err)
                });
        })()
        .catch(err => {
            console.log(err)
        })
    }

    checkTenantChange() {
        if ((this.state.currentTenant === sessionStorage.getItem('tenant_id')) || (parseInt(this.state.currentTenant) === parseInt(sessionStorage.getItem('tenant_id')))) {
            setTimeout(this.checkTenantChange, 100)
        } else {
            this.setState({ 
                currentTenant: sessionStorage.getItem('tenant_id'),
                loading: true
            })
                
            setTimeout(this.updateData, 100)
        }
    }

    updateData() {
        if (this.isMounted) {
            this.getData()
            this.checkTenantChange()
        }
    }

    componentDidMount() {
        this.isMounted = true;
        this.updateData()
    }

    componentWillUnmount() {
        this.isMounted = false;
    }

    render() {
        return (
            <Card className="flex-fill">
                <CardBody className="py-4">
                    <Media>
                        <div className="d-inline-block mt-2 mr-3">
                            <CreditCard className="feather-lg text-success" />
                        </div>
                        <Media body>
                            <h3 className="mb-2">{this.state.subscriptions}</h3>
                            <div className="mb-0">Subscriptions</div>
                        </Media>
                    </Media>
                </CardBody>
            </Card>
        );
    };
}

export default SubscriptionsCountPanel;