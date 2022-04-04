import React from "react";

import { Card, CardBody, Media } from "reactstrap";
import { Globe } from "react-feather";

import { GetTenantsCount } from '../../../controllers/tenants';

class TenantsCountPanel extends React.Component {
    isMounted = false;

    constructor() {
        super();

        this.state = {
            tenants: '0',
            currentTenant: sessionStorage.getItem('tenant_id') ? sessionStorage.getItem('tenant_id') : null
        }

        this.checkTenantChange = this.checkTenantChange.bind(this)
        this.updateData = this.updateData.bind(this)     
    }

    getData() {
        (async () => {
            await GetTenantsCount()
                .then(res => {
                    const tenants = res.data.data;

                    this.setState({ tenants });
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
                            <Globe className="feather-lg text-primary" />
                        </div>
                        <Media body>
                            <h3 className="mb-2">{this.state.tenants}</h3>
                            <div className="mb-0">Tenants</div>
                        </Media>
                    </Media>
                </CardBody>
            </Card>
        );
    };
}

export default TenantsCountPanel;